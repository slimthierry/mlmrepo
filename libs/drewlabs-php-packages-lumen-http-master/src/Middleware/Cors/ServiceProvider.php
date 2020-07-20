<?php

namespace Drewlabs\Packages\Http\Middleware\Cors;

use Drewlabs\Packages\Http\Middleware\Cors\Contracts\ICorsServices;
use Drewlabs\Packages\Http\Middleware\Cors\CorsServices;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/' => config_path(),
        ], 'drewlabs-cors');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ICorsServices::class, function () {
            return new CorsServices(\config('cors', null));
        });
    }
}
