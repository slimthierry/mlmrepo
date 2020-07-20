<?php

namespace Drewlabs\Core\Notification;

use Drewlabs\Contracts\EntityObject\AbstractEntityObject;

class NotificationResult extends AbstractEntityObject
{

    /**
     * @inheritDoc
     */
    protected function getJsonableAttributes()
    {
        return array(
            'mail_id',
            'message_id',
            'status_code',
            'date',
            'error',
            'recipients'
        );
    }
}
