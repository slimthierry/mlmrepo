<?php

namespace Drewlabs\Packages\Identity\Providers;

use Drewlabs\Packages\Identity\Events\Publishers\LoginAttempt;
use Drewlabs\Packages\Identity\Events\Subscribers\LoginAttemptSubscriber;
use Illuminate\Support\ServiceProvider;
use Drewlabs\Packages\Identity\Role;
use Drewlabs\Packages\Identity\Permission;
use Drewlabs\Packages\Identity\User;
use Drewlabs\Packages\Identity\ModelObservers\UserObserver;
use Drewlabs\Packages\Identity\ModelObservers\PermissionObserver;
use Drewlabs\Packages\Identity\ModelObservers\RoleObserver;
use Drewlabs\Contracts\Auth\IAuthenticatableProvider;
use Drewlabs\Contracts\Auth\IUserModel;
use Drewlabs\Packages\Identity\Extensions\IlluminateAuthenticatableProvider;
use Drewlabs\Contracts\Auth\IAuthenticatablePolicy;
use Drewlabs\Contracts\Data\IModelFilter;
use Drewlabs\Contracts\Hasher\IHasher;
use Drewlabs\Core\Auth\UserLockManager;
use Drewlabs\Packages\Identity\Contracts\IUserManager;
use Drewlabs\Packages\Identity\Extensions\IdentityPolicyProvider;
use Drewlabs\Packages\Identity\ModelObservers\OrganisationObserver;
use Drewlabs\Packages\Identity\Organisation;
use Drewlabs\Packages\Identity\UserManager;
use Illuminate\Contracts\Auth\Guard;
use Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository;
use Drewlabs\Packages\Identity\AuthManager;
use Drewlabs\Packages\Identity\Contracts\IAuthManager;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;


class IdentityServiceProvider extends ServiceProvider
{

    private $policies = [
        User::class => \Drewlabs\Packages\Identity\Policies\UsersPolicy::class
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'drewlabs-identity-migrations');
        $this->publishes([
            __DIR__ . '/../config' => base_path('config')
        ], 'drewlabs-identity-configs');
        $this->publishes([
            __DIR__ . '/../views' => resource_path('views')
        ], 'drewlabs-identity-views');
        $this->identityModelsObservers();

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Drewlabs\Packages\Identity\Console\Commands\CreateAdminGroup::class,
                \Drewlabs\Packages\Identity\Console\Commands\CreateAppAdministrator::class,
                \Drewlabs\Packages\Identity\Console\Commands\CreateAppAuthorization::class,
                \Drewlabs\Packages\Identity\Console\Commands\CreateAppAuthorizationGroup::class
            ]);
        }

        // Register identity policies and gate controllers
        $this->registerIsAdminGatePolicy();
        $this->registerCreateUserGateContract();
        $this->registerPolicies();
    }

    public function identityModelsObservers()
    {
        User::observe(UserObserver::class);
        Permission::observe(PermissionObserver::class);
        Role::observe(RoleObserver::class);
        Organisation::observe(OrganisationObserver::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerModels();
        $this->registerIdentityProviders();
        $this->registerDataProviders();

        //
        $this->app->bind(\Drewlabs\Packages\Identity\Events\Subscribers\CreateUserWorkspaceSubscriber::class, function ($app) {
            return new \Drewlabs\Packages\Identity\Events\Subscribers\CreateUserWorkspaceSubscriber($app[\config('drewlabs_identity.workspace.user_workspace.service', '\\Drewlabs\\Packages\\Workspace\\Services\\UserWorkspaceManager')]);
        });
    }

    /**
     * Register identity model instances
     *
     * @return void
     */
    private function registerModels()
    {
        $this->app->bind(IUserModel::class, function () {
            return new User();
        });
    }

    public function registerIdentityProviders()
    {
        $this->app->singleton(IAuthenticatableProvider::class, function ($app) {
            return new IlluminateAuthenticatableProvider($app[IUserModel::class], $app[IHasher::class], (new UserLockManager())->setMaxAttempts(\config('drewlabs_identity.login_attempts', 5)));
        });

        $this->app->singleton(Guard::class, function ($app) {
            return $app['auth']->guard();
        });

        $this->app->singleton(\Drewlabs\Packages\Identity\Contracts\IUserNotificationActionsHandler::class, function ($app) {
            return new \Drewlabs\Packages\Identity\Services\UserNotificationActionsHandlerService();
        });

        // Binding the policy provider
        $this->app->singleton(IAuthenticatablePolicy::class, function ($app) {
            return new IdentityPolicyProvider();
        });

        // Register Drewlabs auth manager bindings for the current application
        $this->app->bind(IAuthManager::class, function ($app) {
            return new AuthManager($app[IAuthenticatableProvider::class], $app[Guard::class], (new LoginAttempt())->subscribe($app[LoginAttemptSubscriber::class]));
        });

        $this->app->bind(IUserManager::class, function ($app) {
            return new UserManager(
                $app[IAuthenticatableProvider::class],
                // $app[UserRepository::class],
                $app[BaseIlluminateModelRepository::class],
                $app[IHasher::class],
                $app[IUserModel::class]
            );
        });
    }

    private function registerCreateUserGateContract()
    {
        // Can use create user with a specific role label
        $this->app[GateContract::class]->define('can-create-user-with-role', function ($user, $roles) {
            # code...
            if ($this->app->make(GateContract::class)->allows('is-admin')) {
                return true;
            }
            $result = $this->app->make(BaseIlluminateModelRepository::class)
                ->setModel(Role::class)
                ->pushFilter(
                    $this->app->make(IModelFilter::class)->setQueryFilters(
                        array(
                            'whereIn' => array('id', $roles)
                        )
                    )
                )->find();
            if (collect($result)->isEmpty()) {
                return true;
            }
            $is_allowed = true;
            collect($result)->each(function ($i) use (&$is_allowed) {
                if ($i->is_admin_user_role === 1) {
                    $is_allowed = false;
                    return;
                }
            });
            return $is_allowed;
        });
    }

    private function registerIsAdminGatePolicy()
    {
        $this->app[GateContract::class]->define('is-admin', function ($user) {
            # code... //
            if ($this->app[IAuthenticatablePolicy::class]->hasPermission($user, \drewlabs_identity_configs('all_authorization'))) {
                return true;
            }
            if ($this->app[IAuthenticatablePolicy::class]->hasRole($user, \drewlabs_identity_configs('admin_group'))) {
                return true;
            }
            return false;
        });
    }

    private function registerPolicies()
    {
        if (isset($this->policies) && !empty($this->policies)) {
            foreach ($this->policies as $key => $value) {
                # code...
                if (!class_exists($key) || !class_exists($value)) continue;
                $this->app[GateContract::class]->policy($key, $value);
            }
        }
    }

    private function registerDataProviders()
    {
        $this->app->when(\Drewlabs\Packages\Identity\Http\Controllers\RolesController::class)
            ->needs(\Drewlabs\Contracts\Data\IDataProvider::class)
            ->give(function () {
                return new \Drewlabs\Core\Data\Services\DrewlabsDataProvider(
                    new \Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository(\Drewlabs\Packages\Identity\Role::class)
                );
            });
        $this->app->bind(\Drewlabs\Packages\Identity\Services\Contracts\IRolesDataProvider::class, function () {
            return new \Drewlabs\Packages\Identity\Services\RolesDataProvider(
                new \Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository(\Drewlabs\Packages\Identity\Role::class)
            );
        });
        $this->app->when(\Drewlabs\Packages\Identity\Http\Controllers\PermissionsController::class)
            ->needs(\Drewlabs\Contracts\Data\IDataProvider::class)
            ->give(function () {
                return new \Drewlabs\Core\Data\Services\DrewlabsDataProvider(
                    new \Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository(\Drewlabs\Packages\Identity\Permission::class)
                );
            });
        $this->app->bind(\Drewlabs\Packages\Identity\Services\Contracts\IDepartmentUsersDataProvider::class, function () {
                return new \Drewlabs\Packages\Identity\Services\DepartmentUsersDataProvider(
                    new \Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository(\Drewlabs\Packages\Identity\DepartmentUser::class)
                );
            });
        $this->app->bind(\Drewlabs\Packages\Identity\Services\Contracts\IDepartmentsDataProvider::class, function () {
            return new \Drewlabs\Packages\Identity\Services\DepartmentsDataProvider(
                new \Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository(\Drewlabs\Packages\Identity\Department::class)
            );
        });
        $this->app->bind(\Drewlabs\Packages\Identity\Services\Contracts\IUserInfosDataProvider::class, function () {
            return new \Drewlabs\Packages\Identity\Services\UserInfosDataProvider(
                new \Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository(\Drewlabs\Packages\Identity\UserInfo::class)
            );
        });
    }
}
