<?php

namespace Drewlabs\Core\Notification\AwsSES\Core;

use Drewlabs\Contracts\EntityObject\AbstractEntityObject;

class AwsSESNotificationServerConfigs extends AbstractEntityObject implements \Drewlabs\Core\Notification\AwsSES\Contracts\IAwsNotificationServerConfigs
{

    use \Drewlabs\Core\Notification\Traits\NotificationServerConfigs;

    /**
     * @inheritDoc
     */
    protected function getJsonableAttributes()
    {
        return array(
            'key',
            'secret',
            'region',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getServerRegion()
    {
        return $this->region;
    }
}
