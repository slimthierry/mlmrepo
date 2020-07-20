<?php //

namespace Drewlabs\Packages\PassportPHPLeagueOAuth;

use Illuminate\Support\ServiceProvider;

/**
 * @package Drewlabs\Packages\PassportPHPLeagueOAuth
 */
class LaravelPassportRoutesServiceProvider extends ServiceProvider
{


    /**
     * @var array
     */
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
        // Register PassportOAuth routes
        (new LaravelPassportOAuthRouteRegistar($this->options))->all();
    }
}
