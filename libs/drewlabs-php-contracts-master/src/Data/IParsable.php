<?php

namespace Drewlabs\Contracts\Data;

interface IParsable
{
    /**
     * Returns the fillable properties of the given model
     *
     * @return array
     */
    public function getFillables();

    /**
     * Dictionnary mapping of the fillable entries of the model and the request inputs
     *
     * @return array
     */
    public function getModelStateMap();
}
