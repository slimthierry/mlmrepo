<?php

namespace Drewlabs\Contracts\Notification;

interface IMailNotification extends INotification
{

    /**
     * Returns a comma sperated list of mail attached receivers
     *
     * @return string
     */
    public function getCc();

    /**
     * Returns the mail subject
     *
     * @return string
     */
    public function getSubject();
}
