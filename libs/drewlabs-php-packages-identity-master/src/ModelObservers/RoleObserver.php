<?php

namespace Drewlabs\Packages\Identity\ModelObservers;

use Drewlabs\Packages\Identity\Role;

class RoleObserver
{


    /**
     * Handle Role Model creating event
     *
     * @param Role $model
     * @return void
     */
    public function creating(Role $model)
    {
        // Verify if the label is unique
        $role = Role::withTrashed()->where(array(array('label', $model->label)))->whereNotNull('deleted_at')->get()->first();
        if (!is_null($role)) {
            // Force delete the role role
            $role->forceDelete();
        }
        $this->setDefaultPropertiesOnSavingOrCreating($model);
    }

    /**
     * Handle Role Model saving event
     *
     * @param Role $model
     * @return void
     */
    public function saving(Role $model)
    {
        $this->setDefaultPropertiesOnSavingOrCreating($model);
    }

    /**
     * Handle role "deleting" event
     *
     * @param Role $model
     * @return void
     */
    public function updating(Role $model)
    {
        if (((int) $model->getKey() === (int) \drewlabs_identity_configs('default_user_role')) || (\drewlabs_identity_configs('admin_group') == $model->label)) {
            return FALSE;
        }
    }

    /**
     * Handle role "deleting" event
     *
     * @param Role $model
     * @return void
     */
    public function deleting(Role $model)
    {
        if (((int) $model->getKey() === (int) \drewlabs_identity_configs('default_user_role')) || (\drewlabs_identity_configs('admin_group') == $model->label)) {
            return FALSE;
        }
        if ($model->isForceDeleting()) {
            $model->permission_roles()->forceDelete();
            $model->role_users()->forceDelete();
            $model->department_roles()->forceDelete();
            return;
        }
        // Remember to always call the method instead of the property
        // Method with return the queried relation while the property will return
        // the the collection of matching data
        $model->role_users()->delete();
        $model->permission_roles()->delete();
        $model->department_roles()->delete();
    }

    private function setDefaultPropertiesOnSavingOrCreating(Role $model)
    {
        $model->deleted_at = null;
        // // If label is not unique delete the role with the given label
        if (is_null($model->display_label) || !isset($model->display_label)) {
            $model->display_label = $model->label;
        }
        if (is_null($model->is_admin_user_role) || !isset($model->is_admin_user_role)) {
            $model->is_admin_user_role = 0;
        }
    }
}
