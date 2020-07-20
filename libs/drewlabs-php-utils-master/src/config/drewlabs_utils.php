<?php


/**
 * This file contains configuration not related to specific packages but utilities environment configuration definitions
 */

return [
    'hash' => [
        'provider' => \env('HASH_PROVIDER', 'bcrypt')
    ]
];
