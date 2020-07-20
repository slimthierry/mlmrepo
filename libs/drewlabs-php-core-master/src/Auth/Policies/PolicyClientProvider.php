<?php

namespace Drewlabs\Core\Auth\Policies;

use Drewlabs\Contracts\Auth\Authenticatable as IAuthenticatable;
use Drewlabs\Contracts\Auth\IAuthenticatablePolicy;

abstract class PolicyClientProvider implements IAuthenticatablePolicy
{
    /**
     * @method
     * Check if the current authenticated user has a certain role
     * @param IAuthenticatable
     * @param string $role
     * @return bool
     */
    public function hasRole(IAuthenticatable $user, $role)
    {
        if (!property_exists($user, 'roles')) {
            return false;
        }
        if (!empty($user->roles)) {
            return in_array($role, iterator_to_array($this->rolesToIterable($user->roles)));
        }
        return false;
    }

    /**
     * Checks if the current authenticated user has a role in a list of roles
     * @param IAuthenticatable
     * @param array[string] $roles
     * @return bool
     */
    public function hasRoleIn(IAuthenticatable $user, array $roles)
    {
        $has_role = false;
        foreach ($roles as $role) {
            if ($this->hasRole($user, $role)) {
                $has_role = true;
                break;
            }
        }
        return $has_role;
    }

    /**
     * Checks if the current authenticated user has a permission in it mapping role and permissions
     * @param IAuthenticatable
     * @param string|int $permission
     * @return bool
     */
    public function hasPermission(IAuthenticatable $user, $permission)
    {
        if (!property_exists($user, 'permissions')) {
            return false;
        }
        if (!empty($user->permissions)) {
            return in_array($permission, iterator_to_array($this->permissionsToIterable($user->permissions)));
        } else {
            return false;
        }
    }

    /**
     * Checks if the current authenticated user has a permission in a list of permission
     * @param IAuthenticatable
     * @param array[string] $permissions
     * @return bool
     */
    public function hasPermissionIn(IAuthenticatable $user, array $permissions)
    {
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if ($this->hasPermission($user, $permission)) {
                $hasPermission = true;
                break;
            }
        }
        return $hasPermission;
    }
    /**
     * Returns an iterator of role label strings
     *
     * @param array[RoleEntity] $user_roles
     * @return \Traversable|array
     */
    private function rolesToIterable(array $user_roles)
    {
        foreach ($user_roles as $role) {
            # code...
            yield $role->label;
        }
    }
    /**
     * Returns an iterator of permissions label strings
     *
     * @param array[PermissionEntity] $user_roles
     * @return \Traversable|array
     */
    private function permissionsToIterable(array $user_permissions)
    {
        foreach ($user_permissions as $permission) {
            # code...
            yield $permission->label;
        }
    }
}
