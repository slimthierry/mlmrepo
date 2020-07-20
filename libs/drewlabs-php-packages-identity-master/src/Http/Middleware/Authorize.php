<?php

namespace Drewlabs\Packages\Identity\Http\Middleware;

use Drewlabs\Packages\Identity\Exceptions\UnAuthorizedExecption;
use Drewlabs\Packages\Identity\Permission;
use Closure;
use Illuminate\Contracts\Auth\Guard;

/**
 * Authorization middleware
 */
class Authorize extends BaseMiddleware
{

 /**
  * Handle an incoming request and check User Authorizations.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  \Closure  $next
  * @param  string[]  ...$guards
  * @return mixed
  */
    public function handle($request, Closure $next, ...$permissions)
    {
        try {
            // Checks if user has a permission in a set of permissions
            if (!$this->authorize($request, $permissions)) {
                // Return an unauthorize access response
                return $this->unauthorized($request);
            } //
        } catch (UnAuthorizedExecption $e) {
            // If an exception occured an unauthorize access response with the execption message
            return $this->unauthorized($request, $e);
        } catch (\RuntimeException $e) {
            // If an exception occured an unauthorize access response with the execption message
            return $this->unauthorized($request, $e);
        }
        // Proceed to the next middleware or request handler
        return $next($request);
    }

    /**
     * Determine if the user is has the permission to perform a given action
     *
     * @param \Illuminate\Http\Request $request
     * @param  array  $scopes
     * @return bool
     *
     * @throws UnAuthorizedExecption
     */
    protected function authorize($request, $scopes)
    {
        if (! $request->user() || ! $request->user()->token()) {
            return false;
        }
        foreach ($scopes as $scope) {
            if ($request->user()->tokenCan($scope)) {
                return true;
            }
        }
        throw new UnAuthorizedExecption('Unauthorized.');
    }
}
