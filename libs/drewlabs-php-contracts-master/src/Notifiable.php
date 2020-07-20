<?php

namespace Drewlabs\Contracts;

/**
 * Interface d'envoi de notifications
 */
interface Notifiable
{
    /**
     * @method
     * @param mixed $receiver
     * @return void
     */
    public function notify($receiver);
}
