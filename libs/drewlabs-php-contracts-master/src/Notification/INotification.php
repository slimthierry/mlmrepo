<?php

namespace Drewlabs\Contracts\Notification;

interface INotification
{
    /**
     * Return the string identifier of the notification sender
     *
     * @return string
     */
    public function getSender();

    /**
     * Returns the notification identifier of the notification receiver
     *
     * @return string|mixed
     */
    public function getReceiver();

    /**
     * Returns the notification content
     *
     * @return string
     */
    public function getContent();
}
