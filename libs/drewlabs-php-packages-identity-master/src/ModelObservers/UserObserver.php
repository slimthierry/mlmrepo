<?php

namespace Drewlabs\Packages\Identity\ModelObservers;

use Drewlabs\Packages\Identity\User;

class UserObserver
{
    /**
     * Handle User Model creating event
     *
     * @param User $model
     * @return void
     */
    public function creating(User $model)
    {
        // Verify if the label is unique
        // $user = User::withTrashed()->where(array(array('user_name', $model->user_name)))->whereNotNull('deleted_at')->get()->first();
        // if (!is_null($user)) {
        //     // Force delete the user user
        //     $user->forceDelete();
        // }
    }

    /**
     * Handle user "deleting" event
     *
     * @param User $user
     * @return void
     */
    public function deleting(User $user)
    {
        // Remember to always call the method instead of the property
        // Method with return the queried relation while the property will return
        // the the collection of matching data
        if ($user->isForceDeleting()) {
            $user->user_roles()->delete();
            $user->user_info()->delete();
            $user->user_channels()->delete();
        }
    }
}
