<?php

namespace Drewlabs\Packages\Identity\Providers;

use Drewlabs\Packages\Identity\Events\Publishers\LoginAttempt;
use Drewlabs\Packages\Identity\Events\Subscribers\LoginAttemptSubscriber;
use Drewlabs\Core\Jwt\JwtAuth;
use Illuminate\Support\ServiceProvider;
use Drewlabs\Contracts\Auth\IAuthenticatableProvider;
use Drewlabs\Packages\Identity\Extensions\AuthGuard;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Container\Container as Application;
use Drewlabs\Contracts\Auth\IAuthenticatablePolicy;
use Drewlabs\Packages\Identity\DefaultScopes;

abstract class AuthenticationServiceProvider extends ServiceProvider
{

    protected function passportAuthExtensionsBingings()
    {
        $this->app['auth']->provider('passport_provider', function ($app, array $config) {
            return $app[IAuthenticatableProvider::class];
        });
    }

    protected function registerRessourcesPolicies(Application $app)
    {
        $this->app[GateContract::class]->before(function ($user, $ability) {
            // This prevent authorization guards to the apply
            // while authorization is not well tested
            if (!boolval(\drewlabs_http_handlers_configs('apply_middleware_policies'))) {
                return true;
            }
        });
        if (isset($this->policies) && !empty($this->policies)) {
            foreach ($this->policies as $key => $value) {
                # code...
                if (!class_exists($key) || !class_exists($value)) continue;
                $app[GateContract::class]->policy($key, $value);
            }
        }
    }

    protected function jwtAuthExtensionBindings()
    {
        $this->app['auth']->provider('jwt_provider', function ($app, array $config) {
            return $app[IAuthenticatableProvider::class];
        });
        $this->app['auth']->extend('jwt', function ($app, $name, array $config) {
            return new AuthGuard($app['auth']->createUserProvider($config['provider']), $app[JwtAuth::class], new LoginAttempt(), new LoginAttemptSubscriber());
        });
    }

    protected function registerHasAdminAccessGate()
    {
        $this->app[GateContract::class]->define('is-admin', function ($user) {
            # code...
            if ($this->app[IAuthenticatablePolicy::class]->hasPermission($user, DefaultScopes::SUPER_ADMIN_SCOPE)) {
                return true;
            }
            return false;
        });
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\Drewlabs\Contracts\Auth\IAuthenticatableSerializer::class, function ($app) {
            return $app[\drewlabs_identity_configs('models.authenticatable.serializer', \Drewlabs\Packages\Identity\Extensions\DefaultAuthenticatableSerializer::class)];
        });
    }
}
