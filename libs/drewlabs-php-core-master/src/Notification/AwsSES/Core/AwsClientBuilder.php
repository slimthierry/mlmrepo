<?php

namespace Drewlabs\Core\Notification\AwsSES\Core;

class AwsClientBuilder
{
    /**
     * Create an instance of {Aws\AwsClientInterface} class for handling connections to AWS SES services
     *
     * @return \Aws\AwsClientInterface
     */
    public static function createClient(array $params, $clientType = 'ses')
    {

        if (strtolower($clientType) === 'ses') {
            return new \Aws\Ses\SesClient(
                array_merge(array('version' => '2010-12-01'), $params)
            );
        }
        throw new \RuntimeException('Unimplemented aws client type' . (string)$clientType);
    }
}
