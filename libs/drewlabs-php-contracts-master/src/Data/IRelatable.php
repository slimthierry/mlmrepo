<?php

namespace Drewlabs\Contracts\Data;

interface IRelatable
{
    /**
     * Get the current model related tables|models
     *
     * @return array
     */
    public function getRelations();
}
