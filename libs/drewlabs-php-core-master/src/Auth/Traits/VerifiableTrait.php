<?php

namespace Drewlabs\Core\Auth\Traits;

trait VerifiableTrait
{
    /**
     * Verification functionnality checks
     *
     * @return boolean
     */
    public function isVerified()
    {
        return ($this->is_verified === 1) || ($this->is_verified === true) ? true : false;
    }
}
