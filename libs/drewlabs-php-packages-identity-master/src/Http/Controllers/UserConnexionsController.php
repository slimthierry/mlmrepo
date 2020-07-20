<?php

use Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository;
use Drewlabs\Packages\Database\Extensions\CustomQueryCriteria;
use Drewlabs\Packages\Identity\Services\AuthService;
use Drewlabs\Packages\Identity\UserConnexion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

class UserConnexionsController extends \Drewlabs\Packages\Http\Controllers\RessourcesBaseController
{

    public function __construct(BaseIlluminateModelRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository->setModel(UserConnexion::class);
        // $this->validator = $validator;
        // Middlewares definitions
        $this->middleware("scope:" . \config('passport.first_party_clients_scope'));
        $this->middleware('policy:all', ['only' => ['getUserLoginAttempts']]);
    }

    /**
     * Handle GET /auth/login/attempts/{username}
     *
     * @param Request $request
     * @param string|null $username
     * @return JsonResponse
     */
    public function getUserLoginAttempts(Request $request, $username = null)
    {
        $unsafeData = ['username' => $username];
        $rules = [
            'username' => ['nullable', 'exists:users,user_name'],
        ];
        $validator = $this->app['validator']->make($unsafeData, $rules, array());
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors()->all());
        }
        if (isset($username)) {
            $query_criteria = new CustomQueryCriteria(array(
                'where' => array(array('identifier', $username))
            ));
        } else {
            $query_criteria = new CustomQueryCriteria(
                array(
                    // Apply orderBy filters
                    'orderBy' => ($request->has('order') && $request->has('by')) ? array('order' => $request->get('order'), 'by' => $request->get('by')) : array('order' => 'desc', 'by' => 'updated_at'),
                )
            );
        }
        return $this->respondOk(
            array(
                'attempts' => isset($username) ? $this->repository->pushFilter($query_criteria)->find() : (
                    $request->has('page') ?  $this->repository->pushFilter($query_criteria)->queryRelation(true)->paginate($request->has('per_page') ? $request->get('per_page') : null) :
                    array(
                        "data" => $this->repository->pushFilter($query_criteria)->queryRelation(false)->find()
                    )
                )
            )
        );
    }
    /**
     * Handle GET /auth/login/latest-attempts
     *
     * @param Request $request
     * @return void
     */
    public function getUserLatestSuccessfulLoginAttempt(Request $request)
    {
        $auth_service = new AuthService($this->app[Guard::class]);
        $query_criteria = new CustomQueryCriteria(array(
            'where' => array(array('identifier', $auth_service->user()->getAuthUserName()), array('user_connexion_status', 1)),
            'orderBy' => ($request->has('order') && $request->has('by')) ? array('order' => $request->get('order'), 'by' => $request->get('by')) : array('order' => 'desc', 'by' => 'updated_at'),
        ));
        return $this->respondOk(
            array(
                'attempts' => $this->repository->pushFilter($query_criteria)->find()->first()
            )
        );
    }
}
