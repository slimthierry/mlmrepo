<?php

namespace Drewlabs\Contracts\Observable;

interface IEvent extends \SplSubject
{
    /**
     * Register a new Observer to the Event
     * @param \SplObserver|\SplObserver[] $observer
     * @return static
     */
    public function subscribe($observer);

    /**
     * Unsubscribe a new Observer from the Event
     * @param \SplObserver $observer
     * @return static
     */
    public function unsubscribe(\SplObserver $observer);

    /**
     * Notify Observer(s) about event
     * @param mixed|null $data
     * @return void
     */
    public function fire($data = null);
}
