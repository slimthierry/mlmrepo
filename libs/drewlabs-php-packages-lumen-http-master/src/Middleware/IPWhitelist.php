<?php

namespace Drewlabs\Packages\Http\Middleware;

use Closure;

class IPWhitelist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$ip_whitelist)
    {
        if (!is_null($request->headers->get('X-Real-IP'))) {
            $remote_address = $request->headers->get('X-Real-IP');
        } else {
            $remote_address = $request->ip();
        }
        if (!\in_array($remote_address, $ip_whitelist)) {
            $message = $request->method() . ' ' . $request->path() . '  Unauthorized access. You don\'t have the required privileges to access the ressource.';
            return response($message, 401);
        }
        return $next($request);
    }
}
