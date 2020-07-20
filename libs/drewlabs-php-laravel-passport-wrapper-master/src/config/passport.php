<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Keys
    |--------------------------------------------------------------------------
    |
    | Passport uses encryption keys while generating secure access tokens for
    | your application. By default, the keys are stored as local files but
    | can be set via environment variables when that is more convenient.
    |
    */

    'private_key' => env('PASSPORT_PRIVATE_KEY'),

    'public_key' => env('PASSPORT_PUBLIC_KEY'),
    // Number of minutes that a refresh token will last
    'refresh_token_ttl' => 10080,
    // Number of minutes access token will last
    'access_token_ttl' => 1440,
    // Number of minutes that a personal access token will last
    'personal_access_token_ttl' => 1440,
    // Personal Access Client id
    'personal_access_client_id' => env('PASSPORT_PERSONAL_CLIENT_ID', 1),
    // The name of tokens created when user uses personal access client
    'personal_client_access_token_name' => env('PASSPORT_PERSONAL_CLIENT_NAME', 'Personnal client'),
    // The identifier of the password client
    'password_client_identifier' => env('PASSPORT_PASSWORD_CLIENT_ID'),
    // The secret of the password client
    'password_client_secret' => env('PASSPORT_PASSWORD_CLIENT_SECRET'),

    /*
     |-----------------------------
     | First party scope identifier
     |-----------------------------
     | Defines the scope identifier used for first party applications
     */
    'first_party_clients_scope' => "*"
];
