<?php

namespace Drewlabs\Core\Notification;

use Drewlabs\Contracts\EntityObject\AbstractEntityObject;
use Drewlabs\Contracts\Notification\INotificationServer;

class NotificationServerConfigs extends AbstractEntityObject implements INotificationServer
{

    use \Drewlabs\Core\Notification\Traits\NotificationServerConfigs;

    /**
     * @inheritDoc
     */
    protected function getJsonableAttributes()
    {
        return array(
            'client_id',
            'secret',
            'port',
            'host',
        );
    }
}
