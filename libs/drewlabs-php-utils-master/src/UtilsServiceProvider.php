<?php

namespace Drewlabs\Utils;

use Illuminate\Support\ServiceProvider;

class UtilsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__ . '/config' => base_path('config')
        ], 'drewlabs-utils-configs');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(\Drewlabs\Contracts\EntityObject\IDtoService::class, function($app) {
            return new \Drewlabs\Core\EntityObject\DtoService();
        });
        $this->app->singleton(\Drewlabs\Contracts\Hasher\IHasher::class, function () {
            return (new \Drewlabs\Core\Hasher\HashFactory())->make(\drewlabs_utils_configs('hash.provider'))->resolve();
        });
        $this->app->singleton(\Drewlabs\Contracts\Storage\IStorage::class, function ($app) {
            return new \Drewlabs\Utils\Cache\CacheStorage($app);
        });
    }
}
