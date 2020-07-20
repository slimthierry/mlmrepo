<?php

namespace Drewlabs\Packages\Database;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Drewlabs\Core\Database\NoSql\DatabaseManager;
use Drewlabs\Packages\Database\Contracts\TransactionUtils;
use Drewlabs\Packages\Database\DataTransactionUtils;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->bindings();
    }

    protected function bindings()
    {
        // Solve issue related to version of MySQL older than the 5.7.7 release or MariaDB older than the 10.2.2.
        $this->app['db']->connection()->getSchemaBuilder()->defaultStringLength(255);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TransactionUtils::class, function ($app) {
            return new DataTransactionUtils($app);
        });
        $this->app->bind(\Drewlabs\Contracts\Data\IModelFilter::class, function ($app) {
            return new \Drewlabs\Packages\Database\Extensions\CustomQueryCriteria();
        });

        $this->app->bind(\Drewlabs\Contracts\Data\DataRepository\Services\IModelAttributesParser::class, function ($app) {
            return new \Drewlabs\Core\Data\Services\ModelAttributesParser($app[\Drewlabs\Contracts\Hasher\IHasher::class]);
        });
        // Register Nosql providers bindings
        $this->noSqlBindings();
    }

    /**
     * Binding for Nosql Data providers
     *
     * @return void
     */
    protected function noSqlBindings()
    {
        // Binding for Noqsl Database Manager
        $this->app->bind('nosqlDb', function () {
            new DatabaseManager(config('database.nosql_driver', 'mongo'));
        });
    }
}
