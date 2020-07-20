<?php

namespace Drewlabs\Packages\Identity;

use Illuminate\Support\Facades\Route;

class LaravelRouteRegistar
{
    /**
     * Register all of the user managements, permissions, roles and authentication routes
     *
     * @return void
     */
    public static function all()
    {
        static::auth();
        Route::group(['prefix' => 'ressources'], function () {
            static::users();
            static::roles();
            static::permissions();
            static::user_infos();
            static::organisations();
            static::departments();
        });
    }

    /**
     * Users Management routes definitions
     *
     * @param mixed
     * @return void
     */
    public static function auth()
    {
        // Users routes definitions
        Route::group(['prefix' => 'auth'], function () {
            Route::group(['prefix' => 'login'], function () {
                Route::post('/', ['uses' => 'AuthenticationController@login']);
                Route::group(['middleware' => 'auth'], function () {
                    Route::get('/attempts/latest', ['uses' => 'UserConnexionsController@getUserLatestSuccessfulLoginAttempt']);
                    Route::get('/attempts/{username?}', ['uses' => 'UserConnexionsController@getUserLoginAttempts']);
                });
            });
            // Password reset routes definitions
            Route::get('/password-reset/{identifier}', ['uses' => 'PasswordsController@get']);
            Route::post('/password-reset', ['uses' => 'PasswordsController@create']);
            Route::put('/password-reset', ['uses' => 'PasswordsController@update']);

            // Two factor authentication routes definitions
            Route::get('/two-factor', ['uses' => 'AuthenticationController@getDoubleAuthCode']);
            Route::post('/two-factor', ['uses' => 'AuthenticationController@validateLogin']);

            // Logout and 2 factor auth update route definitions
            Route::group(['middleware' => 'auth'], function () {
                Route::get('/logout', ['uses' => 'AuthenticationController@logout']);
                Route::get('/user', ['uses' => 'AuthenticationController@user']);
                Route::put('two-factor-activate', ['uses' => 'AuthenticationController@activateDoubleAuth']);
                Route::put('two-factor-deactivate', ['uses' => 'AuthenticationController@deactivateDoubleAuth']);
            });
            // Social authentication routes definitions
            Route::group(['prefix' => 'social'], function () {
                Route::group(['middleware' => 'auth'], function () {
                    Route::get('google', ['uses' => 'SocialAuthenticationsController@authWithGoogle']);
                });
                Route::get('google/callback', ['uses' => 'SocialAuthenticationsController@handleGoogleAuthCallback']);
                Route::get('google/user', ['uses' => 'SocialAuthenticationsController@getGoogleAuthUser']);
            });
        });
    }
    /**
     * Users Management routes definitions
     *
     * @param mixed
     * @return void
     */
    public static function users()
    {
        // Users routes definitions
        Route::post('/users', ['uses' => 'UsersController@create']);
        Route::put('/users/{id}', ['uses' => 'UsersController@update']);
        Route::delete('/users/{id}', ['uses' => 'UsersController@delete']);
        Route::get('/users/{id?}', ['uses' => 'UsersController@get']);
        // Route::group(['prefix' => 'ressources'], function () {
        //     // Route::get('/', ['uses' => 'UsersController@get']);
        // });
    }

    /**
     * User details information management routes definitions
     *
     * @return void
     */
    public static function user_infos()
    {
        Route::put('/user-infos', ['uses' => 'UserInfosController@update']);
        Route::get('/user-infos', ['uses' => 'UserInfosController@get', 'as' => 'get_user_infos']);
        Route::group(['middleware' => 'policy:all,manage-user'], function () {
            Route::put('/user-infos/{id}', 'UserInfosController@update');
        });
        // Route::group(['prefix' => 'ressources'], function () {
        // });
    }

    /**
     * Organisations routes definitions
     *
     * @param mixed
     * @return void
     */
    public static function organisations()
    {
        // Users roles definitions
        Route::post('/organisations', ['uses' => 'OrganisationsController@create']);
        Route::put('/organisations/{id}', ['uses' => 'OrganisationsController@update']);
        Route::delete('/organisations/{id}', ['uses' => 'OrganisationsController@delete']);
        Route::get('/organisations/{id?}', ['uses' => 'OrganisationsController@get', 'as' => 'get_organisations']);
        // Route::group(['prefix' => 'ressources'], function () {
        // });
    }

    /**
     * Organisation department routes definitions
     *
     * @param mixed
     * @return void
     */
    public static function departments()
    {
        // Users roles definitions
        Route::post('/departments', ['uses' => 'DepartmentsController@create']);
        Route::put('/departments/{id}', ['uses' => 'DepartmentsController@update']);
        Route::delete('/departments/{id}', ['uses' => 'DepartmentsController@delete']);
        Route::get('/departments/{id?}', ['uses' => 'DepartmentsController@get', 'as' => 'get_departments']);
        // Route::group(['prefix' => 'ressources'], function () {
        // });
    }

    /**
     * Roles Management routes definitions
     *
     * @param mixed
     * @return void
     */
    public static function roles()
    {
        // Users roles definitions
        Route::post('/roles/', ['uses' => 'RolesController@create']);
        Route::put('/roles/{id}', ['uses' => 'RolesController@update']);
        Route::delete('/roles/{id}', ['uses' => 'RolesController@delete']);
        Route::get('/roles/{id?}', ['uses' => 'RolesController@get', 'as' => 'get_roles']);
        // Route::group(['prefix' => 'ressources'], function () {
        // });
    }

    /**
     * Permissions Management routes definitions
     *
     * @param mixed
     * @return void
     */
    public static function permissions()
    {
        // Users permissions definitions
        Route::post('/permissions', ['uses' => 'PermissionsController@create']);
        Route::put('/permissions/{id}', ['uses' => 'PermissionsController@update']);
        Route::delete('/permissions/{id}', ['uses' => 'PermissionsController@delete']);
        Route::get('/permissions/{id?}', ['uses' => 'PermissionsController@get']);
        Route::group(['middleware' => 'auth'], function () {
            Route::get('/application_permissions', ['uses' => 'PermissionsController@getApplicationPermissions']);
        });
        // Route::group(['prefix' => 'ressources'], function () {
        // });
    }
}
