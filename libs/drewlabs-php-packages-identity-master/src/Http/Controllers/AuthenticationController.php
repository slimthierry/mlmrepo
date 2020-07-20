<?php

namespace Drewlabs\Packages\Identity\Http\Controllers;

use Drewlabs\Packages\Http\Controllers\ApiController as Controller;
use Drewlabs\Packages\Identity\Services\AuthService;
use Drewlabs\Contracts\Auth\IAuthenticatableProvider;
use Drewlabs\Core\Auth\Exceptions\UserAccountLockException;
use Drewlabs\Core\Validator\Contracts\IValidator;
use Drewlabs\Utils\DateUtils;
use Drewlabs\Utils\Rand;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Drewlabs\Packages\Identity\Repository\AccountVerificationRepository;
use Drewlabs\Packages\Database\Extensions\CustomQueryCriteria;
use Drewlabs\Core\Jwt\JwtAuth;
use Drewlabs\Packages\Http\Requests\LoginRequest;
use Drewlabs\Packages\Http\Requests\LoginViaRememberTokenRequest;
use Drewlabs\Packages\Identity\Repository\UserRepository;
use Drewlabs\Packages\Identity\Contracts\IAuthManager;
use Drewlabs\Packages\Identity\Repository\TwoFactorAuthenticationRepository;

class AuthenticationController extends Controller
{


    // /**
    //  * @var UserRepository
    //  */
    // protected $repository;

    /**
     *
     * @var IAuthenticatableProvider
     */
    private $userProvider;

    /**
     * Request validator instance provider
     *
     * @var IValidator
     */
    private $validator;

    // /**
    //  * Undocumented variable
    //  *
    //  * @var Guard
    //  */
    // private $authGuard;

    /**
     * Identity authentication manager class instance
     *
     * @var IAuthManager
     */
    private $manager;

    public function __construct(
        UserRepository $repository,
        IAuthenticatableProvider $userProvider,
        IValidator $validator,
        Guard $authGuard,
        IAuthManager $manager
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->userProvider = $userProvider;
        $this->validator = $validator;
        $this->authGuard = $authGuard;
        $this->manager = $manager;
        $this->middleware(
            "scope:" . \config(
                'passport.first_party_clients_scope'
            ),
            ['only' =>
            ['activateDoubleAuth']]
        );
        $this->middleware(
            "scope:" . \config(
                'passport.first_party_clients_scope'
            ),
            ['only' =>
            ['deactivateDoubleAuth']]
        );
    }

    /**
     * Handle POST /auth/login
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $validator = $this->validator->validate(app(LoginRequest::class), $request->all());
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors());
        }
        try {
            $authenticated = $this->manager->authenticate(
                ['user_name' => $request->get('username'), AuthService::getPasswordPlainTextIndexName() => $request->get('password'), 'request' => $request],
                filter_var($request->get('remember_me'), FILTER_VALIDATE_BOOLEAN)
            );
        } catch (UserAccountLockException $e) {
            return $this->respondOk(array('locked' => true, 'username' => $request->get('username')), null, false);
        }
        if ($authenticated == false) {
            return $this->respondOk(array('authenticated' => false), null, false);
        }
        $user = $this->manager->user();
        if ($user->double_auth_active === 1) {
            $token_string = Rand::appKey(128);
            // Entry is valid for 30 minutes
            $this->app->make(TwoFactorAuthenticationRepository::class)->insert([
                'user' => serialize($user),
                'key' => $token_string,
                'expires_on' => Rand::dateTime("+30 minutes"),
            ]);
            return $this->respondOk(
                array(
                    'login_response' => array('authenticated' => true, 'double_auth_enabled' => true, 'token' => $token_string),
                    'user' => $user
                )
            );
        } else {
            $token = $user->createToken(\drewlabs_passport_configs('personal_client_access_token_name'), ['*']);
            return $this->respondOk(
                array(
                    'login_response' => array('authenticated' => true, 'double_auth_enabled' => false, 'token' => $token->accessToken),
                    'user' => $user
                )
            );
        }
    }

    /**
     * @route POST /auth/login/{id}
     *
     * @param Request $request
     * @param string|int $id
     * @return JsonResponse
     */
    public function loginViaRememberToken(Request $request, $id)
    {
        $validator = $this->validator->validate(app(LoginViaRememberTokenRequest::class), array_merge($request->all(), array('identifier' => $id)));
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors());
        }
        try {
            $authenticated = $this->manager->authenticateViaToken($id, $request->get('remember_token'));
        } catch (UserAccountLockException $e) {
            return $this->respondOk(array('locked' => true, 'username' => $request->get('username')), null, false);
        }
        if ($authenticated == false) {
            return $this->respondOk(array('authenticated' => false), null, false);
        }
        $user = $this->manager->user();
        $token = $user->createToken(\config('passport.personal_client_access_token_name', config('passport.personal_client_access_token_name')), ['*']);
        return $this->respondOk(
            array(
                'login_response' => array('authenticated' => true, 'double_auth_enabled' => $user->double_auth_active, 'token' => $token->accessToken),
                'user' => $user
            )
        );
    }


    /**
     * Handle GET /auth/user
     *
     * @return JsonResponse
     */
    public function user(Request $request)
    {
        try {
            $token = (new \Drewlabs\Core\Jwt\JwtRequestParser())->parse(new \Drewlabs\Packages\Http\Request($request));
            $user = $request->user();
            return $this->respondOk(
                array(
                    'login_response' => array('authenticated' => true, 'double_auth_enabled' => intval($user->double_auth_active) === 1 ? true : false, 'token' => $token),
                    'user' => $user
                )
            );
        } catch (\Exception $th) {
            return $this->respondError($th);
        }
    }

    /**
     * Handle GET /auth/logout
     *
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Revoke current connected user token
            return $this->respondOk(array('data' => $request->user()->token()->revoke() ? intval("1") : 0));
        } catch (\Exception $th) {
            return $this->respondError($th);
        }
    }

    /**
     * Handle GET /auth/two-factor
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDoubleAuthCode(Request $request)
    {
        // TODO write two-factor mechanisme for email, sms and google authenticator
        $validator = $this->app['validator']->make($request->all(), [
            'apikey' => ['required'],
            'validation_type' => ['sometimes'],
        ], array());
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors()->all());
        }
        try {
            $token = $this->app[JwtAuth::class]->parseToken('apikey', null, 'apikey'); # try getting token from the request input and header
        } catch (\RuntimeException $th) {
            return $this->unauthorized($request, $th);
        }
        // Get the double auth entry from the table
        // Only if validation_type is not google authenticator
        $two_factor_auth_filter = new CustomQueryCriteria(
            array(
                'where' => array(array('key', $token))
            )
        );
        $query_result = $this->app->make(TwoFactorAuthenticationRepository::class)->pushFilter($two_factor_auth_filter)->find()->first();
        // if invalid return invalid response to user
        if (!is_null($query_result)) {
            if (DateUtils::from_timestamp(strtotime($query_result->expires_on))->is_past()) {
                $this->app->make(TwoFactorAuthenticationRepository::class)->pushFilter($two_factor_auth_filter)->delete();
                return $this->respondOk(array('session_expired' => true), null, false);
            }
            $user = unserialize($query_result->user);
            $random_code = Rand::int(100000, 999999);
            $this->app->make(AccountVerificationRepository::class)->insert([
                'user' => $user->authIdentifier(),
                'two_factor_auth_validation_code' => $random_code,
                'key' => $query_result->key,
                'expires_on' => Rand::dateTime("+10 minutes"),
            ]);
            $this->app->make(TwoFactorAuthenticationRepository::class)->pushFilter($two_factor_auth_filter)->delete();
            // TODO Send Authentication code to user whith the provided validation type gateway
            // TODO Add google Authenticator library as well for managing code generation and validation
            return $this->respondOk(array('validation_code' => $random_code));
        }
        return $this->respondOk(array(), null, false);
    }

    /**
     * Handle POST /auth/two-factor
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateLogin(Request $request)
    {
        // Validate request input
        $validator = $this->app['validator']->make($request->all(), [
            'code' => ['required', 'numeric'],
            'apikey' => ['required'],
        ], array());
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors()->all());
        }
        try {
            // Try parsing the request to get the temp token provided
            $token = $this->app[JwtAuth::class]->parseToken('apikey', null, 'apikey'); # try getting token from the request input and header
        } catch (\RuntimeException $th) {
            return $this->unauthorized($request, $th);
        }
        // TODO Write validation functionalities
        $verif_code_query_filters = new CustomQueryCriteria(array(
            'where' =>    array(array('two_factor_auth_validation_code', $request->get('code')), array('key', $token))
        ));
        // Load Account verification informations based on conditions builded with user provided data
        $verification_code = $this->app->make(AccountVerificationRepository::class)->pushFilter($verif_code_query_filters)->find()->first();
        if (isset($verification_code)) {
            // if timed out return a response with "validation_time_expired" set to true
            if (!DateUtils::from_timestamp(strtotime($verification_code['expires_on']))->is_future()) {
                return $this->respondOk(array('validation_time_expired' => true, 'double_auth_enabled' => true, 'user' => null, 'token' => null), null, false);
            }
            // In case everything goes well Generate token from the user
            $user_id = $verification_code->user;
            $user = $this->userProvider->findById($user_id);
            $token_string = $this->app[JwtAuth::class]->fromUser($user);
            // Remove the entry from the db storage
            $this->app->make(AccountVerificationRepository::class)->pushFilter($verif_code_query_filters)->delete();
            // Return response to user
            return $this->respondOk(array('authenticated' => true, 'double_auth_enabled' => true, 'user' => $user, 'token' => $token_string));
        }
        return $this->respondOk(array('authenticated' => false, 'double_auth_enabled' => true, 'user' => null, 'token' => null), null, false);
    }

    /**
     * Handle PUT /auth/two-factor-activate
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function activateDoubleAuth(Request $request)
    {
        $auth_service = new AuthService($this->app[Guard::class]);
        $user_repository = $this->app->make(UserRepository::class);
        // Update the user password
        $user_repository->updateById(
            $auth_service->user()->authIdentifier(),
            array('double_auth_active' => 1)
        );
        return $this->respondOk(array());
    }

    /**
     * Handle PUT /auth/two-factor-deactivate
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deactivateDoubleAuth(Request $request)
    {
        $auth_service = new AuthService($this->app[Guard::class]);
        $user_repository = $this->app->make(UserRepository::class);
        // Update the user password
        $user_repository->updateById(
            $auth_service->user()->authIdentifier(),
            array('double_auth_active' => 0)
        );
        return $this->respondOk(array());
    }
}
