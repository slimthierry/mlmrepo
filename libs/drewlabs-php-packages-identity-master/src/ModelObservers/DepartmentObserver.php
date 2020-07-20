<?php

namespace Drewlabs\Packages\Identity\ModelObservers;

use Drewlabs\Packages\Identity\Department;

class DepartmentObserver
{
    /**
     * Handle the Department model "created" event.
     *
     * @param  Department  $model
     * @return void
     */
    public function created(Department $model)
    {
        //
    }

    /**
     * Handle the Department model "updated" event.
     *
     * @param  Department  $model
     * @return void
     */
    public function updated(Department $model)
    {
        //
    }

    /**
     * Handle the Department model "deleted" event.
     *
     * @param  Department  $model
     * @return void
     */
    public function deleted(Department $model)
    {
        //
    }

    /**
     * Handle user "deleting" event
     *
     * @param Department $user
     * @return void
     */
    public function deleting(Department $model)
    {
        // Remember to always call the method instead of the property
        // Method with return the queried relation while the property will return
        // the the collection of matching data
        $model->department_roles()->forceDelete();
        $model->department_user()->forceDelete();
    }

    /**
     * Handle Department Model updating event
     *
     * @param Department $model
     * @return void
     */
    public function updating(Department $model)
    {

    }
}
