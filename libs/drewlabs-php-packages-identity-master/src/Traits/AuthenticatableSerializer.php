<?php

namespace Drewlabs\Packages\Identity\Traits;

use Drewlabs\Contracts\Auth\IUserModel as UserModelContract;
use Drewlabs\Contracts\Auth\IVerifiable as VerifiableContract;
use Drewlabs\Contracts\Auth\IDrewlabsNotifiable as  NotifiableContract;
trait AuthenticatableSerializer
{
    /**
     * {@inheritDoc}
     * @param \Drewlabs\Contracts\Auth\IUserModel $name
     */
    public function deserialize($obj, $withRelated = true)
    {
        $authenticatable = app(\config('drewlabs_identity.models.authenticatable.class'));
        $authenticatable->roles = [];
        $authenticatable->permissions = [];
        if ($obj instanceof UserModelContract) {
            $authenticatable->id = $obj->getIdentifier();
            $authenticatable->username = $obj->getUserName();
            $authenticatable->password = $obj->getPassword();
            $authenticatable->is_active = $obj->getIsActive();
            $authenticatable->remember_token = $obj->getRememberToken();
            $authenticatable->double_auth_active = $obj->getDoubleAuthActive();
        }
        // Enhance toAuthenticatable call in order to load notification channel binded object
        if ($obj instanceof NotifiableContract) {
            $authenticatable->channels = $obj->getChannels();
        }
        // Enhance toAuthenticatable call in order to load notification channel binded object
        if ($obj instanceof VerifiableContract) {
            $authenticatable->is_verified = $obj->isVerified();
        }
        if ($withRelated) {
            // Check if user_roles relation is defined on the model
            if (method_exists($obj, 'user_roles')) {
                // loop through the user_roles relations and set the authenticatable roles array
                foreach ($obj->user_roles as $value) {
                    # code...
                    $role = $value->role;
                    array_push($authenticatable->roles, new \Drewlabs\Core\Auth\RoleEntity($role->id, $role->label, $role->display_label, $role->description));
                    // Checks if the permission_roles relation is defined on the role model
                    if ($role->permission_roles) {
                        // loop through the permission_roles relations and set the authenticatable permissions array
                        foreach ($role->permission_roles as $permission_role) {
                            # code...
                            $permission = $permission_role->permission;
                            array_push($authenticatable->permissions, new \Drewlabs\Core\Auth\PermissionEntity($permission->id, $permission->label, $permission->display_label, $permission->description));
                        }
                    }
                }
            }
        }
        if (method_exists($obj, 'user_info')) {
            $authenticatable->user_info = $obj->user_info;
        }
        return $authenticatable;
    }
}
