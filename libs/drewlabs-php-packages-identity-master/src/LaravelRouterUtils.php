<?php

namespace Drewlabs\Packages\Identity;

use Illuminate\Support\Facades\Route;

class LaravelRouterUtils
{
    /**
     * Register forms and form_controls actions routes
     *
     * @param mixed $callback
     * @return void
     */
    public static function routes()
    {
        Route::group([
            'namespace' => 'Drewlabs\Packages\Identity\Http\Controllers',
            'middleware' => 'api',
            'prefix' => 'api'
        ], function () {
            // Route::group(["middleware" => "scope:" . config('passport.first_party_clients_scope')], function () {
                LaravelRouteRegistar::all();
            // });
        });
    }
}
