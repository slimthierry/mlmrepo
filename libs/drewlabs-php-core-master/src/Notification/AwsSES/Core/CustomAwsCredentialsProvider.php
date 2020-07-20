<?php

namespace Drewlabs\Core\Notification\AwsSES\Core;

use GuzzleHttp\Promise;
use GuzzleHttp\Promise\RejectedPromise;

class CustomAwsCredentialsProvider
{
    /**
     * This method creates authentication credentials from configuration parameters
     *
     * @param string $key
     * @param string $secret
     * @return void
     */
    public static function env($key, $secret, $token = null)
    {
        if (is_null($key) || is_null($secret) || !is_string($key) || !is_string($secret)) {
            return new RejectedPromise(new \Aws\Exception\CredentialsException("aws keys and credentials are not defined"));
        }
        // This function IS the credential provider
        return function () use ($key, $secret, $token) {
            // Use credentials from environment variables, if available
            if ($key && $secret) {
                return Promise\promise_for(
                    new \Aws\Credentials\Credentials($key, $secret, $token)
                );
            }

            $msg = 'Could not find environment variable ' . 'credentials in ' . $key . '/' . $secret;
            return new RejectedPromise(new \Aws\Exception\CredentialsException($msg));
        };
    }
}
