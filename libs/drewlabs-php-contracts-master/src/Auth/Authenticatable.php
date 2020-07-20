<?php

namespace Drewlabs\Contracts\Auth;

interface Authenticatable
{
    /**
     * Get unique identifier key name
     * @return string
     */
    public function authIdentifierName();

    /**
     * Get the unique identifier value
     * @return mixed
     */
    public function authIdentifier();

    /**
     * Get password of the authenticated user
     * @return string
     */
    public function authPassword();
    /**
     * Get string representation of the auth password field
     * @return string
     */
    public function authPasswordName();

    /**
     * Get token of the authenticated user
     *
     * @param string|null $token
     * @return string
     */
    public function rememberToken($token = null);

    /**
     * Get token key name
     * @return string
     */
    public function rememberTokenName();

    /**
     * Get the value of the unique identifying field other than the id
     *
     * @return mixed
     */
    public function getAuthUserName();

    /**
     * Create a new personal access token for the user.
     *
     * @param  string  $name
     * @param  array  $scopes
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    public function createToken($name, array $scopes = []);
}
