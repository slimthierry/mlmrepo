<?php

namespace Drewlabs\Packages\Identity\Extensions;

use Drewlabs\Core\Auth\AuthenticatableProvider;
use Illuminate\Contracts\Auth\Authenticatable as Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as IlluminateProvider;
use Drewlabs\Contracts\Auth\Authenticatable as IAuthenticatable;

final class IlluminateAuthenticatableProvider extends AuthenticatableProvider implements IlluminateProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return Authenticatable
     */
    public function retrieveById($identifier)
    {
        return $this->findById($identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed   $identifier
     * @param  string  $token
     * @return Authenticatable
     */
    public function retrieveByToken($identifier, $token)
    {
        return $this->findByToken($identifier, $token);
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  Authenticatable
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        if ($user instanceof IAuthenticatable) {
            return $this->updateAuthRememberToken($user, $token);
        }
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return Authenticatable
     */
    public function retrieveByCredentials(array $credentials)
    {
        return $this->findByCrendentials($credentials);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if ($user instanceof IAuthenticatable) {
            return $this->validateAuthCredentials($user, $credentials);
        }
    }
}
