<?php

namespace Drewlabs\Contracts\Factory;

interface IFactory
{

 /**
  * Make a new Factory class
  * @param mixed $type
  * @return IFactory
  */
    public function make($type);

    /**
     * Resolve the constructed object
     * @return mixed
     */
    public function resolve();
}
