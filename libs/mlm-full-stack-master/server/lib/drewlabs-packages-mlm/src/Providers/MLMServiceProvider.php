<?php

namespace Drewlabs\Packages\PricingPlans\Providers;

use Drewlabs\Packages\PricingPlans\Models\Example;
use Illuminate\Support\ServiceProvider;

class PricingPlansServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        // $this->publishes([
        //     __DIR__ . '/../database/migrations' => database_path('migrations'),
        // ], 'drewlabs-mlm-migrations');
        // $this->publishes([
        //     __DIR__ . '/../config' => base_path('config'),
        // ], 'drewlabs-mlm-configs');

        if ($this->app->runningInConsole()) {
            $this->commands([]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Services concrete classes bindings
        $this->app->when(\Drewlabs\Packages\MLM\Services\ExamplesDataProvider::class)
            ->needs(\Drewlabs\Contracts\Data\DataRepository\Repositories\IModelRepository::class)
            ->give(function () {
                return new \Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository(Example::class);
            });
        // Services interface bindings
        $this->app->bind(
            \Drewlabs\Packages\MLM\Services\Contracts\IExampleDataProvider::class,
            \Drewlabs\Packages\MLM\Services\ExamplesDataProvider::class
        );
    }
}
