<?php

namespace Drewlabs\Core\Notification;

use Drewlabs\Contracts\EntityObject\AbstractEntityObject;
use Drewlabs\Contracts\Notification\INotificationSender;

class NotificationSenderEntity extends AbstractEntityObject implements INotificationSender
{

    /**
     * @inheritDoc
     */
    protected function getJsonableAttributes()
    {
        return array(
            'identifier',
            'name'
        );
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getUniqueIdentifier()
    {
        return $this->identifier;
    }
}
