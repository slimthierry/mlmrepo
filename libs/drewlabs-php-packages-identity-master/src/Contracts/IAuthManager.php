<?php

namespace Drewlabs\Packages\Identity\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Auth\Authenticatable as IAuthenticatable;
use Drewlabs\Core\Auth\Exceptions\UserAccountLockException;
use Illuminate\Http\Request;

interface IAuthManager
{
    /**
     * Try authenticating user using authentication credentials
     *
     * @param array $credentials
     * @param bool $remember
     *
     * @throws UserAccountLockException
     * @return bool
     */
    public function authenticate(array $credentials, bool $remember);

    /**
     * Log user out of the application
     *
     * @return void
     */
    public function logout(Request $request);

    /**
     * Try authenticating user via it identifier and a remember token
     *
     * @param mixed $id
     * @param string $token
     * @throws UserAccountLockException
     * @return bool
     */
    public function authenticateViaToken($id, $token);

    /**
     * Returns the authenticated user
     *
     * @return Authenticatable|IAuthenticatable
     */
    public function user();

}
