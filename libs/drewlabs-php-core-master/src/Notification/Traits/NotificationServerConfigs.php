<?php

namespace Drewlabs\Core\Notification\Traits;

/**
 * @package Drewlabs\Core\Notification
 * Mixin containing method implementation of the {Drewlabs\Contracts\Notification\INotificationServer} interface
 */
trait NotificationServerConfigs
{
    /**
     * Returns the client unique credential for connecting to notification servers
     *
     * @return string
     */
    public function getClientUniqueIdentifier()
    {
        return $this->client_id;
    }

    /**
     * Returns client secret string
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->secret;
    }

    /**
     * Returns ip/url of the notification server
     *
     * @return string|array|null
     */
    public function getServerHost()
    {
        return $this->host;
    }

    /**
     * Returns notification server web service port
     *
     * @return integer
     */
    public function getServerPort()
    {
        return $this->port;
    }
}
