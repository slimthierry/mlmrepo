<?php

namespace Drewlabs\Packages\UploadedFile\Models\Policies;

use Drewlabs\Packages\Identity\Extensions\IlluminateAuthenticatable as User;
use Drewlabs\Packages\MLM\Models\Example;

class ExamplesPolicy
{

    public function __construct() {}

    /**
     * Determine whether the user can view any Examples.
     *
     * @param  User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can view the Example.
     *
     * @param  User  $user
     * @param  Example  $model
     * @return mixed
     */
    public function view(User $user, Example $model)
    {
        return true;
    }

    /**
     * Determine whether the user can create Examples.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can update the Example.
     *
     * @param  User  $user
     * @param  Example  $model
     * @return mixed
     */
    public function update(User $user, Example $model)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the Example.
     *
     * @param  User  $user
     * @param  Example  $model
     * @return mixed
     */
    public function delete(User $user, Example $model)
    {
        return true;
    }
}
