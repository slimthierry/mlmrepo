<?php

namespace Drewlabs\Core\Auth\Traits;

trait Authenticatable
{

    /**
     * Defines the remember token entry name
     *
     * @return string
     */
    protected function authenticatableRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get Authenticated user unique identifier name
     * @return string
     */
    public function authIdentifierName(): ?string
    {
        return $this->identifier;
    }

    /**
     * Get Authenticated user unique identifier value
     * @return mixed
     */
    public function authIdentifier(): ?string
    {
        return isset($this->{$this->identifier}) ? $this->{$this->identifier} : null;
    }

    /**
     * Get password of the authenticated user
     * @return string
     */
    public function authPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Get string representation of the auth password field
     * @return string
     */
    public function authPasswordName()
    {
        return $this->passwordColumnName;
    }

    /**
     * Get Authenticated user remember token
     *
     * @param string|null
     * @return mixed
     */
    public function rememberToken($token = null)
    {
        if (is_null($token)) {
            return $this->{$this->rememberTokenName()};
        }
        $this->{$this->rememberTokenName()} = $token;
        return;
    }

    /**
     * Get remember token key name
     * @return string
     */
    public function rememberTokenName(): ?string
    {
        return $this->authenticatableRememberTokenName();
    }

    /**
     * Get the value of the unique identifying field other than the id
     *
     * @return mixed
     */
    public function getAuthUserName()
    {
        return $this->{$this->auth_user_name};
    }
}
