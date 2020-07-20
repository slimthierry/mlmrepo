<?php

namespace Drewlabs\Packages\Http;

use Illuminate\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__ . '/config' => base_path('config'),
        ], 'drewlabs-http-configs');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(\Drewlabs\Packages\Http\Controllers\ApiDataProviderController::class)
            ->needs(\Drewlabs\Packages\Http\Contracts\IDataProviderControllerActionHandler::class)
            ->give(function () {
                return new DataProviderControllerActionHandler();
            });
        // Register ViewModel validator providers
        $this->app->bind(\Drewlabs\Core\Validator\Contracts\IValidator::class, function ($app) {
            return new \Drewlabs\Core\Validator\InputsValidator($app['validator']);
        });
        $this->app->when(\Drewlabs\Packages\Http\Controllers\ApiDataProviderController::class)
            ->needs(\Drewlabs\Core\Validator\Contracts\IValidator::class)
            ->give(function ($app) {
                return new \Drewlabs\Core\Validator\InputsValidator($app['validator']);
            });
        $this->app->when(\Drewlabs\Packages\Http\Controllers\ApiDataProviderController::class)
            ->needs(\Drewlabs\Packages\Http\Contracts\IActionResponseHandler::class)
            ->give(function () {
                return new ActionResponseHandler();
            });
    }
}
