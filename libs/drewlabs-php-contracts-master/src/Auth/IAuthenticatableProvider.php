<?php

namespace Drewlabs\Contracts\Auth;

use Drewlabs\Contracts\Auth\Authenticatable;

interface IAuthenticatableProvider
{

    /**
     * Retrieve a user based on it id
     * @param mixed $id
     * @return Authenticatable
     */
    public function findById($id);

    /**
     * Retrieve a user based on a pre-saved token
     * @param int $id
     * @param string $token
     * @return Authenticatable
     */
    public function findByToken(int $id, $token);

    /**
     * Retrieve user based on a certain credentials
     * @param array $credentials
     * @return Authenticatable
     */
    public function findByCrendentials(array $credentials);

    /**
     * Update Remembering token
     * @param Authenticatable $user
     * @param string $token
     * @return void
     */
    public function updateAuthRememberToken(Authenticatable $user, $token);

    /**
     * Validate a user against a certain credentials
     * @param Authenticatable
     * @param array $credentials
     * @return bool
     */
    public function validateAuthCredentials(Authenticatable $user, array $credentials);
}
