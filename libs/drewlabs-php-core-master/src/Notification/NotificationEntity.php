<?php

namespace Drewlabs\Core\Notification;

use Drewlabs\Contracts\EntityObject\AbstractEntityObject;
use Drewlabs\Contracts\Notification\INotificationReceiver;
use Drewlabs\Contracts\Notification\INotificationSender;
use Drewlabs\Contracts\Notification\INotificationServer;
use Drewlabs\Contracts\Notification\Notifiable;

class NotificationEntity extends AbstractEntityObject implements Notifiable
{

    /**
     * @inheritDoc
     */
    protected function getJsonableAttributes()
    {
        return array(
            'to',
            'from',
            // 'cc',
            'server_configs'
        );
    }

    /**
     * Set the notification client server configuration
     *
     * @param INotificationServer $server
     * @return static
     */
    public function setClientServerConfig(INotificationServer $server)
    {
        $this->server_configs = $server;
        return $this;
    }

    /**
     * Add a new receiver to the list of receivers
     *
     * @param INotificationReceiver $receiver
     * @return static
     */
    public function pushNotificationReceiver(INotificationReceiver $receiver)
    {
        $to = $this->to;
        if (isset($to) && is_array($to)) {
            $to = array_push($to, $receiver);
        } else {
            $to = [$receiver];
        }
        $this->to = $to;
        return $this;
    }

    /**
     * Set the details of the notification sender
     *
     * @param INotificationSender $sender
     * @return static
     */
    public function setNotificationSender(INotificationSender $sender)
    {
        $this->from = $sender;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNotificationReceivers()
    {
        return $this->to;
    }

    /**
     * @inheritDoc
     */
    public function getNotificationSender()
    {
        return $this->from;
    }

    /**
     * @inheritDoc
     */
    public function notificationServerConfigs()
    {
        return $this->server_configs;
    }
}
