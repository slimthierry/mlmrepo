<?php

namespace Drewlabs\Contracts\Notification;

interface INotificationSender
{

    /**
     * Get the name associated with the sender unique identifier
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the notification sender unique identifier
     *
     * @return string
     */
    public function getUniqueIdentifier();
}
