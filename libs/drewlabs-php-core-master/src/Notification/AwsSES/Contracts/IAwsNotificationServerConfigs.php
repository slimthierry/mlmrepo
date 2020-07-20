<?php

namespace Drewlabs\Core\Notification\AwsSES\Contracts;

interface IAwsNotificationServerConfigs extends \Drewlabs\Contracts\Notification\INotificationServer
{

    /**
     * Return the AWS SES notification server region
     *
     * @return string
     */
    public function getServerRegion();
}
