<?php

namespace Drewlabs\Packages\Identity\Http\Controllers;

use Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository;
use Drewlabs\Contracts\Hasher\IHasher;
use Drewlabs\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Data\IModelFilter;
use Drewlabs\Packages\Identity\DefaultScopes;
use Drewlabs\Utils\DateUtils;
use Drewlabs\Utils\Rand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PasswordsController extends \Drewlabs\Packages\Http\Controllers\ApiController
{

    /**
     *
     * @var string
     */
    private $accountVerificationClass = \Drewlabs\Packages\Identity\AccountVerification::class;

    /**
     *
     * @var BaseIlluminateModelRepository
     */
    private $repository;

    public function __construct(BaseIlluminateModelRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
        // Middlewares definitions
        // $this->middleware('scope:' . DefaultScopes::MANAGE_PASSWORD_SCOPE, [ 'only' => ['create']]);
        $this->middleware('scope:' . DefaultScopes::MANAGE_PASSWORD_SCOPE, [ 'only' => ['update']]);
    }

    /**
     * Handle GET /auth/password-reset/{identifier}
     *
     * @param Request $request
     * @param string|int $identifier
     * @return JsonResponse
     */
    public function get(Request $request, $identifier)
    {
        $unsafeData = ['username' => $identifier];
        $validator = app('validator')->make($unsafeData, [
            'username' => ['required', 'exists:users,user_name'],
        ], array());
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors()->all());
        }
        //
        $user = (clone $this->repository)
            ->setModel(\Drewlabs\Contracts\Auth\IUserModel::class)
            ->resetScope()
            ->pushFilter(app(IModelFilter::class)->setQueryFilters(
                array('where' => array(array('user_name', $identifier)))
            ))->find()->first();
        if (is_null($user)) {
            return $this->respondOk(array('user_exists' => false), null, false);
        }
        // Make authenticatable object from user model if is defined
        $user = $user->toAuthenticatable();
        // Generate the OTC code
        $random_code = Rand::int(100000, 999999);
        // Create a UserAccount verification code object
        (clone $this->repository)
            ->setModel(\config('drewlabs_identity.models.account_verification.class', $this->accountVerificationClass))
            ->resetScope()
            ->insert([
                'user_id' => $user->authIdentifier(),
                'code' => $random_code,
                'expiration_date' => Rand::dateTime("+15 minutes"),
            ]);
        // Send OTC code to user
        // TODO Send email to user using the email provider using an a Job
        // TODO Remove the password_reset_code entry later
        return $this->respondOk(array('password_reset_code' => $random_code));
    }

    /**
     * Handle POST /auth/password-reset
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $rules = [
            'username' => ['required', 'exists:users,user_name'],
            'code' => ['required'],
        ];
        $validator = app('validator')->make($request->all(), $rules, array());
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors()->all());
        }
        $user = (clone $this->repository)
            ->setModel(\Drewlabs\Contracts\Auth\IUserModel::class)
            ->resetScope()
            ->pushFilter(app(IModelFilter::class)->setQueryFilters(
                array('where' => array(array('user_name', $request->get('username'))))
            ))->find()->first();
        if (is_null($user)) {
            return $this->respondOk(array('user_exists' => false), null, false);
        }
        // Make authenticatable object from user model if is defined
        $user = $user->toAuthenticatable();
        $verif_code_query_filters = app(IModelFilter::class)->setQueryFilters(array(
            'where' => array(array('user_id', $user->authIdentifier()), array('code', (int) $request->get('code')))
        ));
        $acc_r  = (clone $this->repository);
        $verification_code = $acc_r->setModel(\config('drewlabs_identity.models.account_verification.class', $this->accountVerificationClass))
            ->resetScope()->pushFilter($verif_code_query_filters)->find()->first();
        if (isset($verification_code)) {
            if (!DateUtils::from_timestamp(strtotime($verification_code['expiration_date']))->is_future()) {
                $acc_r->pushFilter($verif_code_query_filters)->delete();
                return $this->respondOk(array('code_expired' => true, 'token' => null), null, false);
            }
            $tokenTTL = 60; // Within sixty minutes the token expires
            // Generate token that on has the UPDATE_PASSWORD scope
            \Laravel\Passport\Passport::tokensExpireIn(\Drewlabs\Utils\DateUtils::now()->add_minutes($tokenTTL));
            \Laravel\Passport\Passport::personalAccessTokensExpireIn(\Drewlabs\Utils\DateUtils::now()->add_minutes($tokenTTL));
            $token = $user->createToken(
                \config(
                    'passport.personal_client_access_token_name',
                    config('passport.personal_client_access_token_name')
                ),
                [DefaultScopes::MANAGE_PASSWORD_SCOPE]
            );
            // Cleanup any verification values related to the current user
            $acc_r->pushFilter($verif_code_query_filters)->delete();
            return $this->respondOk(array('user_exists' => true, 'token' => $token->accessToken, 'expires_on' => Rand::dateTime("+$tokenTTL minutes")));
        }
        // Cleanup any verification values related to the current user
        return $this->respondOk(array('user_exists' => false, 'token' => null), null, false);
    }

    /**
     * Handle PUT /auth/password-reset
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $rules = [
            'password' => 'required|confirmed|same:password_confirmation|min:8',
            'password_confirmation' => 'required',
        ];
        $validator = app('validator')->make($request->all(), $rules, array());
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors()->all());
        }
        try {
            $user = $request->user();
        } catch (\RuntimeException $th) {
            return $this->unauthorized($request, $th);
        }
        // Checks if the returned value is an Authenticatable
        if (!($user instanceof Authenticatable)) {
            $request->user()->token()->revoke();
            return $this->respondOk(array('reset_time_expires' => true, 'updated' => false), null, false);
        }
        (clone $this->repository)
            ->setModel(\Drewlabs\Contracts\Auth\IUserModel::class)
            ->resetScope()->updateById(
                $user->authIdentifier(),
                array('user_password' => app(IHasher::class)->make($request->get('password')))
            );
        return $this->respondOk(array('reset_time_expires' => false, 'updated' => true));
    }
}
