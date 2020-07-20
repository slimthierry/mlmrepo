<?php

namespace Drewlabs\Core\Auth\Traits;

use Drewlabs\Contracts\Auth\Authenticatable as IAuthenticatable;
use Drewlabs\Contracts\Auth\IUserModel;
use Drewlabs\Core\Auth\PermissionEntity;
use Drewlabs\Core\Auth\RoleEntity;
use Drewlabs\Core\Auth\User;
use Drewlabs\Contracts\Auth\IUserAccountModel;

class AuthenticatableProviderTrait
{
    /**
     * Creates an authenticatable instance from the result of the IUserModel query
     * @param IUserModel|IUserAccountModel $user
     * @return IAuthenticatable
     */
    protected function setAuthenticatableFromModel($model)
    {
        $authenticatable = new User();
        // Set non complex fields
        $authenticatable->id = $model->getIdentifier();
        $authenticatable->username = $model->getUserName();
        $authenticatable->password = $model->getPassword();
        $authenticatable->is_active = $model->getIsActive();
        $authenticatable->remember_token = $model->getRememberToken();
        $authenticatable->double_auth_active = $model->getDoubleAuthActive();
        $authenticatable->roles = [];
        $authenticatable->permissions = [];
        // Check if user_roles relation is defined on the model
        if (method_exists($model, 'user_roles')) {
            // loop through the user_roles relations and set the authenticatable roles array
            foreach ($model->user_roles as $value) {
                # code...
                $role = $value->role;
                array_push($authenticatable->roles, new RoleEntity($role->id, $role->label, $role->display_label, $role->description));
                // Checks if the permission_roles relation is defined on the role model
                if ($role->permission_roles) {
                    // loop through the permission_roles relations and set the authenticatable permissions array
                    foreach ($role->permission_roles as $permission_role) {
                        # code...
                        $permission = $permission_role->permission;
                        array_push($authenticatable->permissions, new PermissionEntity($permission->id, $permission->label, $permission->display_label, $permission->description));
                    }
                }
            }
        }
        return $authenticatable;
    }
}
