<?php

namespace Drewlabs\Core\Jwt;

use Drewlabs\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Auth\IAuthenticatableProvider;
use Drewlabs\Contracts\Http\IRequest;
use Drewlabs\Contracts\Observable\IEvent;
use Drewlabs\Contracts\Observable\ISubscriber;

abstract class JwtAuthGuard
{

    /**
  * Entity provider for the guard
  *
  * @var IAuthenticatableProvider
  */
    protected $provider;

    /**
     * Authenticatable entity instance
     *
     * @var Authenticatable
     */
    protected $user;

    /**
     * Authenticatable entity instance
     *
     * @var Authenticatable
     */
    protected $temp_user;

    /**
     * @var JwtAuth
     */

    protected $auth;

    /**
     * The request wrapper instance
     *
     * @var IRequest
     */
    protected $request;

    /**
     * Attempting event instance
     *
     * @var IEvent
     */
    protected $attempt;

    /**
     *
     * @var ISubscriber
     */
    protected $attempt_listener;

    /**
     * JWTAuthGuard instance constructor
     *
     * @param IAuthenticatableProvider $provider
     * @param JwtAuth $auth
     * @param IEvent $login_attempt_subject
     * @param ISubscriber $login_attempts_subscriber
     */
    public function __construct(
        IAuthenticatableProvider $provider,
        JwtAuth $auth,
        IEvent $login_attempt_subject = null,
        ISubscriber $login_attempts_subscriber = null
    ) {
        $this->provider = $provider;
        $this->auth = $auth;
        $this->attempt = $login_attempt_subject;
        $this->attempt_listener = $login_attempts_subscriber;
        $this->registerAttemptSubscribers();
    }

    /**
     * Check if the current user is authenticated
     *
     * @return Authenticatable
     */
    public function authenticate()
    {
        if (!is_null($user = $this->user())) {
            return $user;
        }
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user()
    {
        //If user has already been set, we just return it
        if (!is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $token = $this->auth->parseToken();
        if (!empty($token)) {
            $user = $this->auth->toUser($token);
        }
        return $this->user = $user;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        if ($this->user()) {
            return $this->user()->authIdentifier();
        }
    }

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function once(array $credentials = [])
    {
        $valid = false;
        if ($this->validate($credentials)) {
            $this->setUser($this->temp_user);
            $valid = true;
        }
        $credentials['status'] = $valid;
        // Fire an observable with the result of the authentication result
        $this->fireAttemptEvent($credentials);
        return $valid;
    }

    /**
     * Fire the attempt event with the arguments.
     *
     * @param  array  $credentials
     * @param  bool  $remember
     * @return void
     */
    protected function fireAttemptEvent(array $credentials)
    {
        if (isset($this->attempt)) {
            $this->attempt->fire($credentials);
        }
    }

    /**
     * Register attemp event subscribers
     * @return void
     */
    public function registerAttemptSubscribers()
    {
        // Register for events
        if (isset($this->attempt) && isset($this->attempt_listener)) {
            $this->attempt->subscribe($this->attempt_listener);
        }
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $this->temp_user = $user = $this->provider->findByCrendentials($credentials);
        return is_null($user) ? false : $this->hasValidCredentials($user, $credentials);
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param  Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    protected function hasValidCredentials(Authenticatable $user, $credentials)
    {
        return !is_null($user) && $this->provider->validateAuthCredentials($user, $credentials);
    }

    /**
     * Set the current user.
     *
     * @param  Authenticatable  $user
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * set the current request
     * @param IRequest
     * @return static
     */
    public function setRequest(IRequest $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get the user provider used by the guard.
     *
     * @return IAuthenticatableProvider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set the user provider used by the guard.
     *
     * @param  IAuthenticatableProvider  $provider
     * @return void
     */
    public function setProvider(IAuthenticatableProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Logout a given user
     *
     * @return void
     */
    public function logout($method = 'bearer')
    {
        try {
            $token = $this->auth->parseToken($method);
            $this->auth->invalidate($token);
        } catch (\Exception $th) {
            return;
        }
    }

    public function __destruct()
    {
        unset($this->attempt);
    }
}
