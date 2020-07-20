<?php

namespace Drewlabs\Core\Observable;

use Drewlabs\Contracts\Observable\IEvent;

class SubjectProvider implements IEvent
{

    /**
     * Event data
     * @var mixed
     */
    private $data;

    /**
     * List of event observers
     * @var array
     */
    private $observers;

    /**
     * Mixin providing attach and detach functionalities
     */
    use \Drewlabs\Core\Observable\Traits\Subjectable;

    public function __construct($data = null)
    {
        $this->data = $data;
        $this->observers = array();
    }

    /**
     * Register a new observer to the event
     * @param \SplObserver|\SplObserver[] $observer
     * @return static
     */
    public function subscribe($observer)
    {
        if (is_array($observer)) {
            foreach ($observer as $v) {
                # code...
                $this->attach($v);
            }
        } else {
            $this->attach($observer);
        }
        return $this;
    }

    /**
     * Unsubscribe Observer from the event
     * @param \SplObserver $observer
     * @return static
     */
    public function unsubscribe(\SplObserver $observer)
    {
        $this->detach($observer);
        return $this;
    }

    /**
     * Notify Observer(s) about event
     * @param mixed $data
     * @return void
     */
    public function fire($data = null)
    {
        $this->data = $data;
        $this->notify();
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    public function __destruct()
    {
        foreach ($this->observers as $value) {
            $this->unsubscribe($value);
        }
        $this->data = null;
    }
}
