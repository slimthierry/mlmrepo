<?php

namespace Drewlabs\Contracts\Auth;

use Drewlabs\Contracts\Auth\Authenticatable;

interface IAuthenticatableInstanciatable
{
    /**
     * Get user model from an instance of the Authenticatable class
     *
     * @param Authenticatable $authenticatable
     * @return static
     */
    public function fromAuthenticatable(Authenticatable $authenticatable);

    /**
     * Build an authenticatable instance from the curren object
     *
     * @param bool $loadRelations
     * @return Authenticatable
     */
    public function toAuthenticatable(bool $loadRelations = true);
}
