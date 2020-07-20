<?php

namespace Drewlabs\Packages\Identity\Contracts;

use Drewlabs\Packages\Identity\User;
use Drewlabs\Contracts\Data\DataRepository\Repositories\IModelRepository;
use Drewlabs\Contracts\Auth\IUserModel;


interface IUserManager
{
    /**
     * Abstract declaration that must provide functionnality for creating a new application user
     *
     * @param array $userModelEntries
     * @param array $configurationEntries
     * @return User
     */
    public function createUser(array $userModelEntries, array $configurationEntries);

    /**
     * Undocumented function
     *
     * @param mixed $userId
     * @param array $userModelEntries
     * @param array $configurationEntries
     * @return User
     */
    public function updateUser($userId, array $userModelEntries, array $configurationEntries = null);

    /**
     * Helper method for finding Application user by a remember token
     *
     * @param mixed $id
     * @param string $token
     * @return User
     */
    public function findUserByRemenberToken($id, $token);

    /**
     * Abstarct method declaration for finding an application user by credentials
     *
     * @param array $credentials
     * @return User
     */
    public function findUserByCredentials(array $credentials);

    /**
     * Return the user repository attached to the current user manager
     *
     * @return IModelRepository
     */
    public function getUserRepository();

    /**
     * Get application identity user model
     *
     * @return IUserModel
     */
    public function getIdentityUserModel();
}
