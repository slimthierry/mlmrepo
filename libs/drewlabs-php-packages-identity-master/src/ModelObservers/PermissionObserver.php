<?php

namespace Drewlabs\Packages\Identity\ModelObservers;

use Drewlabs\Packages\Identity\Permission;

class PermissionObserver
{

    /**
     * Handle Example Model deleting event
     *
     * @param Example $model
     * @return void
     */
    public function creating(Permission $model)
    {
        // Verify if the label is unique
        $permission = Permission::withTrashed()->where(array(array('label', $model->label)))->whereNotNull('deleted_at')->get()->first();
        if (!is_null($permission)) {
            // Force delete the permission permission
            $permission->forceDelete();
        }
        if (is_null($model->display_label) || !isset($model->display_label)) {
            $model->display_label = $model->label;
        }
    }

    /**
     * Handle permission "deleting" event
     *
     * @param Permission $model
     * @return void
     */
    public function deleting(Permission $model)
    {
        if ($model->isForceDeleting()) {
            $model->permission_roles()->forceDelete();
            return;
        }
        // Remember to always call the method instead of the property
        // Method with return the queried relation while the property will return
        // the the collection of matching data
        $model->permission_roles()->delete();
    }
}
