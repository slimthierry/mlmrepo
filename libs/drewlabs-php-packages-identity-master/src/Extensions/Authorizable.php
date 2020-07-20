<?php

namespace Drewlabs\Packages\Identity\Extensions;

use Illuminate\Contracts\Auth\Access\Gate;

trait Authorizable
{
    /**
     * Determine if the entity has a given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function can($ability, $arguments = [])
    {
        return app(Gate::class)->forUser($this)->check($ability, $arguments);
    }

    /**
     * Determine if the entity does not have a given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function cant($ability, $arguments = [])
    {
        return !$this->can($ability, $arguments);
    }

    /**
     * Determine if the entity does not have a given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function cannot($ability, $arguments = [])
    {
        return $this->cant($ability, $arguments);
    }


    /**
     * @inheritDoc
     */
    public function getPermissions()
    {
        return isset($this->permissions) ? $this->permissions : [];
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return isset($this->roles) ? $this->roles : [];
    }

    /**
     * @inheritDoc
     */
    public function permissionPropertyName()
    {
        return \config('drewlabs_identity.authorization.property.permission', 'permissions');
    }

    /**
     * @inheritDoc
     */
    public function permissionGroupPropertyName()
    {
        return \config('drewlabs_identity.authorization.property.permissionGroup', 'roles');
    }
}
