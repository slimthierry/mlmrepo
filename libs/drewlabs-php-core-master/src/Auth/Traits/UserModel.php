<?php

namespace Drewlabs\Core\Auth\Traits;

trait UserModel
{

    /**
     * Fetch user from data storage matching the provided id
     *
     * @return static
     */
    public function getUserById($id)
    {
        return $this->findWith(array(array($this->getPrimaryKey(), $id)), true)->first();
    }

    /**
     * Fetch user by Credentials
     *
     * @param array $credentials
     * @return static
     */
    public function fetchUserByCredentials(array $credentials)
    {
        if (!isset($credentials[static::getUserUniqueIdentifier()])) {
            $identifier = static::getUserUniqueIdentifier();
            throw new \RuntimeException("ERROR [Invalid argument] : Expected credentials[$identifier]");
        }
        $conditions = [];
        array_push($conditions, array(static::getUserUniqueIdentifier(), $credentials[static::getUserUniqueIdentifier()]));
        if ($this->active_field) {
            array_push($conditions, array($this->active_field, 1));
        }
        return $this->findWith($conditions, true)->first();
    }
    /**
     * Update the user remember_token in the data storage
     *
     * @return void
     */
    public function updateUserRememberToken($id, $token)
    {
        $user = $this->getUserById($id);
        $user->{$this->remember_token_field} = $token;
        if ($user->timestamps) {
            $user->timestamps = false;
        }
        $user->save();
    }

    /**
     * Get the auth model unique identifier string
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->{static::getUserUniqueIdentifier()};
    }
    /**
     * Get the auth model hashed password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->{$this->password_field};
    }
    /**
     * Get the auth model activatation value
     *
     * @return int
     */
    public function getIsActive()
    {
        return $this->{$this->active_field};
    }
    /**
     * Get the auth model double authentication value
     *
     * @return int
     */
    public function getDoubleAuthActive()
    {
        return $this->{$this->double_auth_active_field};
    }
    /**
     * Get the auth model rembering token value
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->{$this->remember_token_field};
    }

    /**
     * User unique identifying key
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->{$this->getPrimaryKey()};
    }
}
