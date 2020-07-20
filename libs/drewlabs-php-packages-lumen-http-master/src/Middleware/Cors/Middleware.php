<?php

namespace Drewlabs\Packages\Http\Middleware\Cors;

use Drewlabs\Packages\Http\Middleware\Cors\Contracts\ICorsServices;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class Middleware
{

    /**
  * @var ICorsServices
  */
    private $service;

    /**
     * CorsMiddleware constructor.
     *
     * @param CorsService $service
     */
    public function __construct(ICorsServices $service)
    {
        $this->service = $service;
    }
    /**
     * Passerelle d'écriture des en-têtes HTTP pour les accès CORS
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Checks if the request is a core request
        if (!$this->service->isCorsRequest($request)) {
            return $next($request);
        }

        if ($this->service->isPreflightRequest($request)) {
            $response = new Response();
        } else {
            $response = $next($request);
        }
        return $this->service->handleRequest($request, $response);
    }
}
