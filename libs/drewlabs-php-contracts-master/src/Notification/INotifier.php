<?php

namespace Drewlabs\Contracts\Notification;

interface INotifier
{

    /**
     * Handle notification sending process
     *
     * @return mixed
     */
    public function notify();

    /**
     * Set the notification object on the current notifier instance
     *
     * @param Notifiable $value
     * @return static
     */
    public function setNotification($value);

    /**
     * Rerturns the noitification object of the current notifier instance
     *
     * @return Notifiable
     */
    public function getNotifiable();
}
