<?php

namespace Drewlabs\Packages\Http;

use Illuminate\Contracts\Container\Container;

class HttpUtils
{
    public static function routes(Container $app)
    {
        $app->{'router'}->group([
            'namespace' => 'Drewlabs\\Packages\\Http\\Controllers',
            'prefix' => 'api'
        ], function ($router) {
            $router->get('is_unique', 'UniqueValueController@get');
        });
    }
}
