<?php

namespace Drewlabs\Packages\Identity\Providers;

use Illuminate\Support\ServiceProvider;
use Drewlabs\Packages\Identity\LaravelRouterUtils;
use Drewlabs\Packages\Identity\LumenRouterUtils;

class IdentityRouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if (\is_lumen($this->app)) {
            LumenRouterUtils::routes($this->app);
            return;
        }
        LaravelRouterUtils::routes();
    }
}
