<?php

namespace Drewlabs\Core\Observable;

abstract class SubscriberProvider implements \Drewlabs\Contracts\Observable\ISubscriber
{
    public function listen($data = null)
    {
        throw new \RuntimeException("Not implemented method");
    }

    /**
     * Respond to an Event call
     * @return mixed
     */
    public function update(\SplSubject $event)
    {
        return $this->listen($event->data);
    }
}
