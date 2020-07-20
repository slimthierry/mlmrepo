<?php

namespace Drewlabs\Core\Jwt\Events;

use Drewlabs\Core\Observable\SubscriberProvider;

abstract class AttemptingSubscriber extends SubscriberProvider
{
    /**
     * Respond to an Event call
     *
     * @param mixed $credentials
     * @return void
     */
    public function listen($credentials)
    {
        throw new \RuntimeException("Not implemented method");
    }
}
