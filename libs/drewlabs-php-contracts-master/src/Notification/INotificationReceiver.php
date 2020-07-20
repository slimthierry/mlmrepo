<?php

namespace Drewlabs\Contracts\Notification;

interface INotificationReceiver
{

    /**
     * Get the unique identifier of the notification receiver
     *
     * @return mixed
     */
    public function getReceiver();

    /**
     * Returns the attached receivers of the current notification
     *
     * @return string|array|null
     */
    public function getAttachedReceivers();

    /**
     * Return the content being send through the notification service
     *
     * @return string|mixed
     */
    public function getNotificationContent();

    /**
     * Return the notification subject
     *
     * @return string
     */
    public function getSubject();
}
