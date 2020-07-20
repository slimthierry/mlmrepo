<?php

namespace Drewlabs\Contracts\Notification;

interface INotificationJobProvider
{
    /**
     * Bind/Set the notification object to the job provider
     *
     * @param INotification $mail
     * @return static
     */
    public function setNotificationObject(INotification $mail);
}
