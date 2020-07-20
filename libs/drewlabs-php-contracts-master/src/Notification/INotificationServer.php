<?php


namespace Drewlabs\Contracts\Notification;

interface INotificationServer
{
    /**
     * Returns the client unique credential for connecting to notification servers
     *
     * @return string
     */
    public function getClientUniqueIdentifier();

    /**
     * Returns client secret string
     *
     * @return string
     */
    public function getClientSecret();

    /**
     * Returns ip/url of the notification server
     *
     * @return string|array|null
     */
    public function getServerHost();

    /**
     * Returns notification server web service port
     *
     * @return integer
     */
    public function getServerPort();

}
