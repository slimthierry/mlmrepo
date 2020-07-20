<?php

namespace Drewlabs\Packages\Identity\Traits;

trait IlluminateAuthenticatable
{
    /**
     * @inheritDoc
     */
    public function getFillables()
    {
        return \config('drewlabs_identity.models.authenticatable.fillables', $this->fillables ? $this->fillables : array());
    }

    /**
     * @inheritDoc
     */
    public function getGuarded()
    {
        return $this->guards ? $this->guards : array();
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->authIdentifierName();
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->authIdentifier();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->authPassword();
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->rememberToken();
    }

    /**
     * Set the token value for the "remember me" request.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        return static::rememberToken($value);
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return $this->rememberTokenName();
    }
}
