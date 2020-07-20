<?php

namespace Drewlabs\Packages\Identity\Services;

use Drewlabs\Packages\Identity\Extensions\IlluminateAuthenticatable;
use Drewlabs\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Drewlabs\Contracts\Auth\Authenticatable as IAuthenticatable;
use Drewlabs\Contracts\Auth\IAuthenticatableProvider;

class AuthService
{
    /**
     * @var Guard $guard
     */
    private $guard;

    /**
     * @param Guard
     * @return void
     */
    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Try authenticating user using set of credentials
     * @param array $credentials
     * @return mixed
     */
    public function authenticate(IAuthenticatableProvider $provider,array $credentials)
    {
        if (count($credentials) == 0) {
            throw new \RuntimeException('Authentication credentials must be an array');
        }
        $user = $provider->findByCrendentials($credentials);
        return !is_null($user) && $provider->validateAuthCredentials($user, $credentials) ? $user : false;
    }
    /**
     * Login user
     *
     * @param Authenticatable
     * @param bool $remember
     * @return void
     */
    public function login(Authenticatable $user, bool $remember = false)
    {
        if (method_exists($this->guard, 'login')) {
            $this->guard->{"login"}($user, $remember);
        }
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user()
    {
        $user = $this->guard->user();
        if ($user instanceof IAuthenticatable)
            return IlluminateAuthenticatable::cloneWithouthGuards($user);
        return $user;
    }
    /**
     * Logout the current user
     *
     * @return void
     */
    public function logout()
    {
        if (method_exists($this->guard, 'logout')) {
            $this->guard->{"logout"}();
        }
    }

    /**
     * Reurns the name of the key of the password field in the credentials array
     *
     * @return string
     */
    public static function getPasswordPlainTextIndexName()
    {
        return IlluminateAuthenticatable::getPasswordColumnName();
    }
}
