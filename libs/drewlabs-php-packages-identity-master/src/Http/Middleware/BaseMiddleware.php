<?php

namespace Drewlabs\Packages\Identity\Http\Middleware;

use Drewlabs\Contracts\Auth\IAuthenticatablePolicy;
use Drewlabs\Packages\Identity\Http\Response\UnAuthorizedResponse;
use Drewlabs\Packages\Identity\Http\Response\UnAuthenticatedResponse;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Container\Container as Application;

/**
 * Midleware de base
 */
class BaseMiddleware
{

    use UnAuthorizedResponse;
    use UnAuthenticatedResponse;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @var Factory
     */
    protected $auth;

    /**
     * Application container
     *
     * @var Application
     */
    protected $app;

    /**
     * Policy provider
     * @var IAuthenticatablePolicy
     */
    protected $policy;

    /**
     * Create a new BaseMiddleware instance.
     *
     * @param Dispatcher  $events
     * @param Factory  $auth
     */
    public function __construct(Dispatcher $events, Auth $auth, Application $app, IAuthenticatablePolicy $policy)
    {
        $this->events = $events;
        $this->auth = $auth;
        $this->app = $app;
        $this->policy = $policy;
    }
}
