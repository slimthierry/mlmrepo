<?php

namespace Drewlabs\Packages\Identity\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Drewlabs\Packages\Identity\Exceptions\AuthenticationException;

/**
 * Authentication middleware
 */
class Authenticate extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Determine if the current user is a guess, meaning is not authenticated
        try {
            $this->authenticate($guards);
        } catch (AuthenticationException $e) {
            // If an exception occured an unauthorize access response with the execption message
            return $this->unauthenticated($request, $e);
        } catch (\RuntimeException $e) {
            // If an exception occured an unauthorize access response with the execption message
            return $this->unauthenticated($request, $e);
        }
        // Proceed to the next middleware or request handler
        return $next($request);
    }

    /**
     * Determine if the user is ged in to any of the given guards.
     *
     * @param  array  $guards
     * @return void
     *
     * @throws AuthenticationException
     */
    protected function authenticate(array $guards)
    {
        if ($this->app[Guard::class]->guest()) {
            throw new AuthenticationException('Unauthenticated.', 401);
        }
    }
}
