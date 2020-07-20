<?php

namespace Drewlabs\Packages\Identity\ModelObservers;

use Drewlabs\Packages\Identity\UserInfo;

class UserInfoObserver
{
    /**
     * Handle UserInfo Model creating event
     *
     * @param UserInfo $model
     * @return void
     */
    public function creating(UserInfo $model)
    {
    }

    /**
     * Handle UserInfo "deleting" event
     *
     * @param UserInfo $user
     * @return void
     */
    public function deleting(UserInfo $model)
    {
        // Remember to always call the method instead of the property
        // Method with return the queried relation while the property will return
        // the the collection of matching data
        if ($model->isForceDeleting()) {
        }
        // $user->user_channels()->delete();
    }
}
