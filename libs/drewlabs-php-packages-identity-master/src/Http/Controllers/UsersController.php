<?php

namespace Drewlabs\Packages\Identity\Http\Controllers;

use Drewlabs\Contracts\Auth\IAuthenticatablePolicy;
use Drewlabs\Packages\Http\Controllers\RessourcesBaseController as Controller;
use Drewlabs\Contracts\Hasher\IHasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Drewlabs\Packages\Database\Extensions\CustomQueryCriteria;
use Drewlabs\Utils\Rand;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Drewlabs\Packages\Identity\Http\Requests\UserRequest;
use Drewlabs\Packages\Identity\Contracts\IUserManager;
use Drewlabs\Packages\Identity\DefaultScopes;
use Drewlabs\Contracts\Auth\IUserModel;
use Drewlabs\Core\Validator\Contracts\IValidator;
use Drewlabs\Packages\Identity\Contracts\IUserNotificationActionsHandler;

class UsersController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var IValidator
     */
    private $validator;

    /**
     * User management provider instance
     *
     * @var IUserManager
     */
    private $manager;

    /**
     * Identity policy provider
     *
     * @var IAuthenticatablePolicy
     */
    private $policy;

    /**
     * @var IUserNotificationActionsHandler
     */
    private $notificationActionHandler;

    public function __construct(
        IUserManager $manager,
        IValidator $validator,
        IAuthenticatablePolicy $policy,
        IUserNotificationActionsHandler $handler
    ) {
        parent::__construct();
        $this->manager = $manager;
        $this->validator = $validator;
        $this->policy = $policy;
        $this->notificationActionHandler = $handler;
        // $this->middleware('policy:all,manage-users,list-users')->only('get');
        // $this->middleware('policy:all')->only('update');
        // $this->middleware("scope:" . \config('passport.first_party_clients_scope'));
        $this->middleware('policy:all,create-users', ['only' => ['create']]);
        $this->middleware('policy:all,delete-users', ['only' => ['delete']]);
    }

    /**
     * Checks if a user with the permission to CREATE_USER can create a user with the role_label parameter
     *
     * @param string[]|int[] $roles
     * @return boolean
     */
    private function canCreateUserPolicy(array $roles)
    {
        return app()[GateContract::class]->allows('can-create-user-with-role', array($roles));
    }

    /**
     * Handle POST /users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        // validate request inputs
        $username = $request->has('username') ? $request->get('username') : $request->get('email');
        if (!(\filter_var($username, FILTER_VALIDATE_EMAIL) === false)) {
            // Username value is an email, should generate
            $username = \explode('@', $username)[0] . Rand::int(100, 999);
        }
        $roles = !$request->has('roles') ? [] : $request->get('roles');
        if (\drewlabs_identity_configs('set_default_user_role')) {
           $roles = array_unique(array_merge($roles, [\drewlabs_identity_configs('default_user_role')]));
        } else {
            $roles = empty($roles) ? [\drewlabs_identity_configs('default_user_role')] : $roles;
        }
        $request = $request->merge(['roles' => $roles]);
        $validator = $this->validator->validate(
            app(\config('drewlabs_identity.requests.create_user.rules.class', UserRequest::class)),
            array_merge($request->all(), array('username' => $username))
        );
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors());
        }
        // Front gate controller for user creation action
        if ($request->has('roles')) {
            if (!$this->canCreateUserPolicy($request->get('roles'))) {
                return $this->unauthorized($request);
            }
        }
        try {
            $user = $request->user();
            $request_has_password = ($request->has("password")) ? true : false;
            $password = $request->get("password");
            $password = !$request_has_password ? Rand::password() : $request->get("password");
            $userModelEntries = [
                "user_name" => $username,
                "user_password" => app()[IHasher::class]->make($password),
                "is_active" => $request->get("is_active"),
                "is_verified" => $request->get('is_verified'),
                "remember_token" => null,
                "double_auth_active" => 0,
                "created_by" => $user->authIdentifier(),
            ];
            // Create the user using the user manager service provider
            $result = $this->manager->createUser($userModelEntries, $request->all());
            if ($request->has('workspaces')) {
                \drewlabs_dispatch_event(
                    (new \Drewlabs\Packages\Identity\Events\Publishers\CreateUserWorkspaceEvent())->subscribe(
                        app(\Drewlabs\Packages\Identity\Events\Subscribers\CreateUserWorkspaceSubscriber::class)
                    ),
                    array(
                        'workspaces' => $request->get('workspaces'),
                        'user' => $result,
                        'action'  => 'USER_CREATE'
                    )
                );
            }
            // Trigger send create user notification action
            $this->notificationActionHandler->sendOnCreateUser($request_has_password, [
                'email' => $request->get('email'),
                'username' => $username,
                'password' => $password
            ]);
            // Delay the execution for .3 seconds in order to load user added workspaces
            sleep(.1);
            return $this->respondOk(array('data' => $result instanceof \Drewlabs\Contracts\Auth\IUserModel ? $result->toAuthenticatable() : $result));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle PUT /users/{id}
     *
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $validator = $this->validator->setUpdate(true)->validate(app(UserRequest::class), $request->all());
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors());
        }
        try {
            if (!$request->user()->can('update', $this->manager->getUserRepository()->findById($id))) {
                return $this->unauthorized($request);
            }
            $result = $this->manager->updateUser($id, array_filter($request->all(), function($param){
                return !is_null($param);
            }));
            // Put workspace configs in the cache
            if ($request->has('workspaces') && isset($result)) {
                \drewlabs_dispatch_event(
                    (new \Drewlabs\Packages\Identity\Events\Publishers\CreateUserWorkspaceEvent())->subscribe(
                        app(\Drewlabs\Packages\Identity\Events\Subscribers\CreateUserWorkspaceSubscriber::class)
                    ),
                    array(
                        'workspaces' => $request->get('workspaces'),
                        'user' => $result,
                        'action'  => 'USER_UPDATE'
                    )
                );
            }
            // Delay the execution for .1 seconds in order to load user added workspaces
            sleep(.1);
            if ($result instanceof \Drewlabs\Contracts\Auth\IUserModel) {
                $result = app(\Drewlabs\Contracts\Auth\IUserModel::class)->getUserById($result->getIdentifier())->toAuthenticatable();
            }
            return $this->respondOk(array('data' => $result));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle requests coming to DELETE /users/{id}
     *
     * @param Request $request
     * @param [type] $id
     * @return JsonResponse
     */
    public function delete(Request $request, $id)
    {
        try {
            if (!$request->user()->can('delete', $this->manager->getUserRepository()->findById($id))) {
                return $this->unauthorized($request);
            }
            $result = $this->manager->getUserRepository()->deleteById($id);
            return $this->respondOk(array('data' => $result));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle GET /users/{id?}
     *
     * @return JsonResponse
     */
    public function get(Request $request, $id = null)
    {
        if (isset($id)) {
            if (!$request->user()->can('view', $this->manager->getUserRepository()->findById($id))) {
                return $this->unauthorized($request);
            }
            $user = $this->manager->getUserRepository()->findById($id);
            return $this->respondOk(array('data' => is_null($user) ? $user : $user->toAuthenticatable()));
        }
        $filters = array(
            // Apply orderBy filters
            'where' => array(
                array('user_id', '<>', $request->user()->authIdentifier())
            ),
            'orderBy' => ($request->has('order') && $request->has('by')) ? array('order' => $request->get('order'), 'by' => $request->get('by')) : array('order' => 'desc', 'by' => 'updated_at'),
        );
        $filters = array_merge($filters, app(IUserModel::class)->parseRequestQueryFilters($request));
        $filters['whereHas'][] = array('user_roles.role.permission_roles.permission', function ($query) use ($request) {
            $query->where('label', '<>', DefaultScopes::SUPER_ADMIN_SCOPE);
        });
        // TODO : Application logic need to be refactor
        if (\drewlabs_identity_configs('apply_users_filter')) {
            if (!$this->policy->hasPermission($request->user(), DefaultScopes::SUPER_ADMIN_SCOPE)) {
                $current_user_info = $this->manager->getIdentityUserModel()->fromAuthenticatable($request->user())->user_info;
                if (!is_null($current_user_info->department_user)) {
                    $filters['whereHas'][] = array('user_info', function ($query) use ($current_user_info) {
                        $query->where('department_id', '=', $current_user_info->department_user->department_id);
                    });
                    // Only show the users of the same agence as the connected user
                    $filters['whereHas'][] = array('user_info', function ($query) use ($current_user_info) {
                        $query->where('agence_id', '=', $current_user_info->agence_id);
                    });
                } else if ($request->has('permission') && $this->policy->hasPermission($request->user(), $request->get('permission'))) {
                    // If user has a given permission and is querying of that permission users returns
                    $filters['whereHas'][] = array('user_roles.role.permission_roles.permission', function ($query) use ($request) {
                        $query->where('label', $request->get('permission'));
                    });
                } else {
                    return $this->respondOk(array('users' => ['data' => []]));
                }
            } else {
                if ($request->has('permission')) {
                    $filters['whereHas'][] = array('user_roles.role.permission_roles.permission', function ($query) use ($request) {
                        $query->where('label', $request->get('permission'));
                    });
                }
            }
        } else {
            if ($request->has('permission')) {
                $filters['whereHas'][] = array('user_roles.role.permission_roles.permission', function ($query) use ($request) {
                    $query->where('label', $request->get('permission'));
                });
            }
        }
        if ($request->has('page')) {
            $users = $this->manager->getUserRepository()->{"pushFilter"}(new CustomQueryCriteria($filters))->queryRelation($request->has('without_relations') ? false : true)->paginate($request->has('per_page') ? $request->get('per_page') : null);
            $items = $users->items();
            $users = $users->toArray();
            if (!empty($users["data"])) {
                $users["data"] = collect($items)->map(function ($value) use ($request) {
                    return $value->toAuthenticatable($request->has('without_relations') ? false : true);
                });
            }
        } else {
            $result = $this->manager->getUserRepository()->{"pushFilter"}(new CustomQueryCriteria($filters))->queryRelation($request->has('without_relations') ? false : true)->find();
            if (!empty($result)) {
                $result = collect($result)->map(function ($value) use ($request) {
                    return $value->toAuthenticatable($request->has('without_relations') ? false : true);
                });
            }
            $users = array_merge(array(), array("data" => $result));
        }
        return $this->respondOk($users);
    }
}
