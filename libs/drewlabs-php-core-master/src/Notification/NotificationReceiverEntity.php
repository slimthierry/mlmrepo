<?php

namespace Drewlabs\Core\Notification;

use Drewlabs\Contracts\EntityObject\AbstractEntityObject;
use Drewlabs\Contracts\Notification\INotificationReceiver;

class NotificationReceiverEntity extends AbstractEntityObject implements INotificationReceiver
{

    /**
     * @inheritDoc
     */
    protected function getJsonableAttributes()
    {
        return array(
            'identifier',
            'cc',
            'subject',
            'content',
        );
    }

    /**
     * @inheritDoc
     */
    public function getReceiver()
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function getAttachedReceivers()
    {
        return $this->cc;
    }

    /**
     * @inheritDoc
     */
    public function getNotificationContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
