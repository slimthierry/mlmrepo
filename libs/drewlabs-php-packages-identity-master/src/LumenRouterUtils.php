<?php

namespace Drewlabs\Packages\Identity;

class LumenRouterUtils
{
    /**
     * Register forms and form_controls actions routes
     *
     * @param mixed $callback
     * @return void
     */
    public static function routes($callback)
    {
        if (\is_lumen($callback)) {
            $callback = $callback->router;
        }
        $callback = $callback ?: function ($router) {
            $router->all();
        };
        $callback->{'group'}([
            'namespace' => 'Drewlabs\Packages\Identity\Http\Controllers',
            'prefix' => 'api'
        ], function () use ($callback) {
            // $callback->{"group"}(["middleware" => "scope:" . config('passport.first_party_clients_scope')], function () use ($callback) {
                LumenRouteRegistar::all($callback);
            // });
        });
    }
}
