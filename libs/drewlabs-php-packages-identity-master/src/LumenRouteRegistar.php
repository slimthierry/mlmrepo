<?php

namespace Drewlabs\Packages\Identity;

class LumenRouteRegistar
{
    /**
     * Register all of the user managements, permissions, roles and authentication routes
     *
     * @return void
     */
    public static function all($router)
    {
        $router->group(['prefix' => 'auth'], function () use ($router) {
            $router->group(['prefix' => 'login'], function () use ($router) {
                $router->post('/', ['uses' => 'AuthenticationController@login']);
                $router->group(['middleware' => 'auth'], function () use ($router) {
                    $router->get('/attempts/latest', ['uses' => 'UserConnexionsController@getUserLatestSuccessfulLoginAttempt']);
                    $router->get('/attempts[/{username}]', ['uses' => 'UserConnexionsController@getUserLoginAttempts']);
                });
            });
            // Password reset routes definitions
            $router->get('/password-reset/{identifier}', ['uses' => 'PasswordsController@get']);
            $router->post('/password-reset', ['uses' => 'PasswordsController@create']);
            $router->put('/password-reset', ['uses' => 'PasswordsController@update']);

            // Two factor authentication routes definitions
            $router->get('/two-factor', ['uses' => 'AuthenticationController@getDoubleAuthCode']);
            $router->post('/two-factor', ['uses' => 'AuthenticationController@validateLogin']);

            // Logout and 2 factor auth update route definitions
            $router->group(['middleware' => 'auth'], function () use ($router) {
                $router->get('/logout', ['uses' => 'AuthenticationController@logout']);
                $router->get('/user', ['uses' => 'AuthenticationController@user']);
                $router->put('/two-factor-activate', ['uses' => 'AuthenticationController@activateDoubleAuth']);
                $router->put('/two-factor-deactivate', ['uses' => 'AuthenticationController@deactivateDoubleAuth']);
            });
            // Social authentication routes definitions
            $router->group(['prefix' => 'social'], function () use ($router) {
                $router->group(['middleware' => 'auth'], function () use ($router) {
                    $router->get('/google', ['uses' => 'SocialAuthenticationsController@authWithGoogle']);
                });
                $router->get('/google/callback', ['uses' => 'SocialAuthenticationsController@handleGoogleAuthCallback']);
                $router->get('/google/user', ['uses' => 'SocialAuthenticationsController@getGoogleAuthUser']);
            });
        });
        // Users routes definitions
        $router->group(['prefix' => 'ressources'], function ($router) {
            $router->post('/users', ['uses' => 'UsersController@create']);
            $router->put('/users/{id}', ['uses' => 'UsersController@update']);
            $router->delete('/users/{id}', ['uses' => 'UsersController@delete']);
            $router->get('/users[/{id}]', ['uses' => 'UsersController@get']);

            // User infos routes definitions
            $router->put('/user-infos', ['uses' => 'UserInfosController@update']);
            $router->get('/user-infos', ['uses' => 'UserInfosController@get']);
            $router->group(['middleware' => 'policy:all,manage-user'], function () use ($router) {
                $router->put('/user-infos/{id}', 'UserInfosController@update');
            });

            // Organisations routes definitions
            $router->post('/organisations', ['uses' => 'OrganisationsController@create']);
            $router->put('/organisations/{id}', ['uses' => 'OrganisationsController@update']);
            $router->delete('/organisations/{id}', ['uses' => 'OrganisationsController@delete']);
            $router->get('/organisations[/{id}]', ['uses' => 'OrganisationsController@get']);

            // Departments routes definitions
            $router->post('/departments', ['uses' => 'DepartmentsController@create']);
            $router->put('/departments/{id}', ['uses' => 'DepartmentsController@update']);
            $router->delete('/departments/{id}', ['uses' => 'DepartmentsController@delete']);
            $router->get('/departments[/{id}]', ['uses' => 'DepartmentsController@get']);

            // Users roles routes definitions
            $router->get('/roles[/{id}]', ['uses' => 'RolesController@get', 'as' => 'get_roles']);
            $router->post('/roles', ['uses' => 'RolesController@create']);
            $router->put('/roles/{id}', ['uses' => 'RolesController@update']);
            $router->delete('/roles/{id}', ['uses' => 'RolesController@delete']);

            // Users permissions routes definitions
            $router->post('/permissions', ['uses' => 'PermissionsController@create']);
            $router->put('/permissions/{id}', ['uses' => 'PermissionsController@update']);
            $router->delete('/permissions/{id}', ['uses' => 'PermissionsController@delete']);
            $router->get('/permissions[/{id}]', ['uses' => 'PermissionsController@get']);

            $router->group(['middleware' => 'auth'], function () use ($router) {
                $router->get('/application_permissions', ['uses' => 'PermissionsController@getApplicationPermissions']);
            });
        });
    }
}
