<?php

/*
|--------------------------------------------------------------------------
| Configuration definitions for binding to application controllers
|--------------------------------------------------------------------------
|
| This file contains definition for request actions bindings for ressources
| used in  the application
|
*/
return [
    "requests" => [
        "posts" => null
    ],
    "auth_middleware" => env('AUTH_MIDDLEWARE', 'auth'),
    "apply_middleware_policies" => env('APPLY_POLICIES', false)
];
