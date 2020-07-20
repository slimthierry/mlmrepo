<?php

namespace Drewlabs\Core\Auth;

use Drewlabs\Contracts\Auth\Authenticatable as IAuthenticatable;
use Drewlabs\Core\Auth\Traits\Authenticatable;

abstract class User implements IAuthenticatable, \JsonSerializable
{
    const PASSWORD_PLAIN = 'password';

    /**
     * @deprecated 1.1.0
     */
    const REMEMBER_TOKEN_NAME = 'remember_token';

    use Authenticatable;
    /**
     * Unique identifier of the user
     */
    public $id;
    /**
     * Authenticated user unique identifying name
     *
     * @var string
     */
    public $username;
    /**
     * Authenticated user password
     *
     * @var string
     */
    public $password;
    /**
     * Authenticated user active or not
     *
     * @var int
     */
    public $is_active;
    /**
     * Remembering token of the user stored in the database
     *
     * @var string
     */
    public $remember_token;
    /**
     * Double authentication active or not
     *
     * @var string
     */
    public $double_auth_active;
    /**
     * Name of the identifier of the user in the data storage
     *
     * @var string
     */
    public $identifier = 'id';
    /**
     * Pasword field name
     *
     * @var string
     */
    public $passwordColumnName = self::PASSWORD_PLAIN;

    /**
     * Authenticated user other unique identiying key
     *
     * @var string
     */
    public $auth_user_name = 'username';
    /**
     * Array of RoleEntity class
     *
     * @var array[RoleEntity]
     */
    public $roles;
    /**
     * Array of PermssionEntity class
     *
     * @var array[PermssionEntity]
     */
    public $permissions;

    /**
     * An object representing extra informations about the application user
     */
    public $user_info;


    /**
     * List of notication channels that have been configure on a user
     */
    public $channels;

    /**
     * Returns the name of the password field
     *
     * @return string
     */
    public static function getPasswordColumnName()
    {
        return self::PASSWORD_PLAIN;
    }

    /**
     * Clone a user with guarded field removed
     *
     * @param IAuthenticatable $user
     * @return static
     */
    public static function cloneWithouthGuards(IAuthenticatable $user)
    {
        return $user;
    }

    /**
     * Return the fillable properties of the current object
     *
     * @return array
     */
    abstract public function getFillables();

    /**
     * Return guarded properties of the current object
     *
     * @return array
     */
    abstract public function getGuarded();

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $tags = array();
        foreach ($this->getFillables() as $k) {
            if (in_array($k, $this->getGuarded())) {
                continue;
            }
            if (property_exists($this, $k)) {
                $tags[$k] = $this->{$k};
            }
        }
        return $tags;
    }
}
