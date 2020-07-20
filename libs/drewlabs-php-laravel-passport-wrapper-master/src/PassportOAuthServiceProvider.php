<?php

namespace Drewlabs\Packages\PassportPHPLeagueOAuth;

use Drewlabs\Packages\PassportPHPLeagueOAuth\Console\Commands\Purge;
use Drewlabs\Packages\PassportPHPLeagueOAuth\Console\Commands\TablesMigration;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Connection;

/**
 * Class CustomQueueServiceProvider
 * @package App\Providers
 */
class PassportOAuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations'),
        ], 'drewlabs-oauth-migrations');

        $this->publishes([
            __DIR__.'/config' => base_path('config'),
        ], 'drewlabs-oauth-configs');

        $this->app->singleton(Connection::class, function() {
            return $this->app['db.connection'];
        });
        if (preg_match('/5\.[678]\.\d+/', $this->app->version())) {
            $this->app->singleton(\Illuminate\Hashing\HashManager::class, function ($app) {
                return new \Illuminate\Hashing\HashManager($app);
            });
        }
        if ($this->app->runningInConsole()) {
            $this->commands([
                Purge::class,
                TablesMigration::class,
                \Drewlabs\Packages\PassportPHPLeagueOAuth\Console\Commands\PassportScopesConfigure::class
            ]);
        }
    }
    /**
     * @return void
     */
    public function register()
    {
    }
}
