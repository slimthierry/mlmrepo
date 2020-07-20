<?php //

namespace Drewlabs\Packages\PassportPHPLeagueOAuth;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

/**
 * @package Drewlabs\Packages\PassportPHPLeagueOAuth
 */
class LumenPassportRoutesServiceProvider extends ServiceProvider
{

    protected $options = [];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
    /**
     * @return void
     */
    public function register()
    {
        $this->routes($this->app, $this->options);
    }


    /**
     * Get a Passport route registrar.
     *
     * @param  callable|Router|Application  $callback
     * @param  array  $options
     * @return PassportOAuthRouteRegistar
     */
    public function routes($callback = null, array $options = [])
    {
        $defaultOptions = [
            'prefix' => PassportOAuthUtils::$routePrefix,
            'namespace' => '\Laravel\Passport\Http\Controllers',
        ];
        if (\is_lumen($callback)) {
            $callback = $callback->router;
        }
        $options = array_merge($defaultOptions, $options);
        $callback->group(Arr::except($options, ['namespace']), function ($router) use ($options) {
            (new LumenPassportOAuthRouteRegistar($router, $options))->all();
        });
    }
}
