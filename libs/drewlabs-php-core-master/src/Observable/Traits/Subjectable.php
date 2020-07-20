<?php


namespace Drewlabs\Core\Observable\Traits;

trait Subjectable
{

    /**
     * Add new observer to the list of observers
     * @param \SplObserver $observer
     * @return void
     */
    public function attach(\SplObserver $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     * Removes an observer from the list of observers
     * @param \SplObserver $observer
     * @return void
     */
    public function detach(\SplObserver $observer)
    {
        $key = array_search($observer, $this->observers, true);
        if ($key) {
            unset($this->observers[$key]);
        }
    }
    
    /**
     * Notify event subject subscribers
     * @return void
     */
    public function notify()
    {
        foreach ($this->observers as $value) {
            $value->update($this);
        }
    }
}
