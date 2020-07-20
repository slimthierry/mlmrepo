<?php

namespace Drewlabs\Packages\PassportPHPLeagueOAuth;
use Illuminate\Support\Facades\Route;

class LaravelPassportOAuthRouteRegistar
{

    /**
     * @var array
     */
    private $options;
    /**
     * Create a new route registrar instance.
     *
     * @param  $app
     * @param  array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }
    /**
     * Register routes for transient tokens, clients, and personal access tokens.
     *
     * @return void
     */
    public function all()
    {
        $this->forAccessTokens();
        $this->forTransientTokens();
        $this->forClients();
        $this->forPersonalAccessTokens();
    }
    /**
     * @param string $path
     * @return string
     */
    private function prefix($path)
    {
        if (strstr($path, '\\') === false && isset($this->options['namespace'])) return $this->options['namespace'] . '\\' . $path;
        return $path;
    }
    /**
     * Register the routes for retrieving and issuing access tokens.
     *
     * @return void
     */
    public function forAccessTokens()
    {
        // Call the issueToken method of the \Drewlabs\Packages\PassportPHPLeagueOAuth\Http\Controllers\PassportOAuthAccessTokenController instead of passport own
        Route::post('/token', $this->prefix('\Drewlabs\Packages\PassportPHPLeagueOAuth\Http\Controllers\PassportOAuthAccessTokenController@issueToken'));
        Route::group(['middleware' => ['auth']], function () {
            Route::get('/tokens', $this->prefix('AuthorizedAccessTokenController@forUser'));
            Route::delete('/tokens/{token_id}', $this->prefix('AuthorizedAccessTokenController@destroy'));
        });
    }
    /**
     * Register the routes needed for refreshing transient tokens.
     *
     * @return void
     */
    public function forTransientTokens()
    {
        Route::post('/token/refresh', [
            'middleware' => ['auth'],
            'uses' => $this->prefix('TransientTokenController@refresh')
        ]);
    }
    /**
     * Register the routes needed for managing clients.
     *
     * @return void
     */
    public function forClients()
    {
        Route::group(['middleware' => ['auth']], function () {
            Route::get('/clients', $this->prefix('ClientController@forUser'));
            Route::post('/clients', $this->prefix('ClientController@store'));
            Route::put('/clients/{client_id}', $this->prefix('ClientController@update'));
            Route::delete('/clients/{client_id}', $this->prefix('ClientController@destroy'));
        });
    }
    /**
     * Register the routes needed for managing personal access tokens.
     *
     * @return void
     */
    public function forPersonalAccessTokens()
    {
        Route::group(['middleware' => ['auth']], function () {
            Route::get('/scopes', $this->prefix('ScopeController@all'));
            Route::get('/personal-access-tokens', $this->prefix('PersonalAccessTokenController@forUser'));
            Route::post('/personal-access-tokens', $this->prefix('PersonalAccessTokenController@store'));
            Route::delete('/personal-access-tokens/{token_id}', $this->prefix('PersonalAccessTokenController@destroy'));
        });
    }

    /**
     * Register the routes needed for managing scopes.
     *
     * @return void
     */
    public function forScopes()
    {
        Route::group(['middleware' => ['auth']], function () {
            // Route::get('/scopes[/{id}]', $this->prefix('\\Drewlabs\\Packages\\PassportPHPLeagueOAuth\\Http\Controllers\\PassportScopesController@get'));
            Route::post('/scopes', $this->prefix('\\Drewlabs\\Packages\\PassportPHPLeagueOAuth\\Http\Controllers\\PassportScopesController@create'));
            Route::put('/scopes/{id}', $this->prefix('\\Drewlabs\\Packages\\PassportPHPLeagueOAuth\\Http\Controllers\\PassportScopesController@update'));
            Route::delete('/scopes/{id}', $this->prefix('\\Drewlabs\\Packages\\PassportPHPLeagueOAuth\\Http\Controllers\\PassportScopesController@delete'));
        });
    }
}
