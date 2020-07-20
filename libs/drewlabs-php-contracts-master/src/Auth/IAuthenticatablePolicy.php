<?php

namespace Drewlabs\Contracts\Auth;

use Drewlabs\Contracts\Auth\Authenticatable as IAuthenticatable;

interface IAuthenticatablePolicy
{
    /**
     * @method
     * Check if the current authenticated user has a certain role
     * @param IAuthenticatable
     * @param string $role
     * @return bool
     */
    public function hasRole(IAuthenticatable $user, $role);

    /**
     * Checks if the current authenticated user has a role in a list of roles
     * @param IAuthenticatable
     * @param array[string] $roles
     * @return bool
     */
    public function hasRoleIn(IAuthenticatable $user, array $roles);

    /**
     * Checks if the current authenticated user has a permission in it mapping role and permissions
     * @param IAuthenticatable
     * @param string|int $permission
     * @return bool
     */
    public function hasPermission(IAuthenticatable $user, $permission);

    /**
     * Checks if the current authenticated user has a permission in a list of permission
     * @param IAuthenticatable
     * @param array[string] $permissions
     * @return bool
     */
    public function hasPermissionIn(IAuthenticatable $user, array $permissions);

    /**
     * Checks if a given user has an administrator role
     *
     * @param IAuthenticatable $user
     * @return boolean
     */
    public function hasAdminRole(IAuthenticatable $user);
}
