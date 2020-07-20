<?php

namespace Drewlabs\Packages\Identity\Policies;

use Drewlabs\Packages\Identity\Extensions\IlluminateAuthenticatable;
use Drewlabs\Packages\Identity\User;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class UsersPolicy
{
    // /**
    //  * Injected [[IUserManager]] instance
    //  *
    //  * @var IUserManager
    //  */
    // private $manager;

    /**
     * @var GateContract
     */
    private $gate;

    public function __construct(GateContract $gate)
    {
        // $this->manager = $manager;
        $this->gate = $gate;
    }
    /**
     * Determine whether the user can view any Users.
     *
     * @param  IlluminateAuthenticatable  $user
     * @return mixed
     */
    public function viewAny(IlluminateAuthenticatable $user)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can view the User.
     *
     * @param  IlluminateAuthenticatable  $user
     * @param  User  $model
     * @return mixed
     */
    public function view(IlluminateAuthenticatable $user, User $model)
    {
        //
        if ($this->gate->allows('is-admin')) {
            return true;
        }
        // $user_id = $user->authIdentifier();
        return $model->created_by === $user->authIdentifier();;
    }

    /**
     * Determine whether the user can create Users.
     *
     * @param  IlluminateAuthenticatable  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can update the User.
     *
     * @param  IlluminateAuthenticatable  $user
     * @param  User  $model
     * @return mixed
     */
    public function update(IlluminateAuthenticatable $user, User $model)
    {
        //
        if ($this->gate->allows('is-admin')) {
            return true;
        }
        return $model->created_by === $user->authIdentifier();;
    }

    /**
     * Determine whether the user can delete the User.
     *
     * @param  IlluminateAuthenticatable  $user
     * @param  User  $model
     * @return mixed
     */
    public function delete(IlluminateAuthenticatable $user, User $model)
    {
        //
        if ($this->gate->allows('is-admin')) {
            return true;
        }
        // $user_id = $user->authIdentifier();
        return $model->created_by === $user->authIdentifier();;
    }
}
