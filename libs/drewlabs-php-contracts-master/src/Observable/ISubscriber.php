<?php


namespace Drewlabs\Contracts\Observable;

interface ISubscriber extends \SplObserver
{

    /**
     * Listen for Events Updates
     * @param mixed|null $data
     * @return mixed
     */
    public function listen($data = null);
}
