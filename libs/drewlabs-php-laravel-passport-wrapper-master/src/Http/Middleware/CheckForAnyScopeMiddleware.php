<?php

namespace Drewlabs\Packages\PassportPHPLeagueOAuth\Http\Middleware;

use Drewlabs\Packages\Identity\Http\Response\UnAuthorizedResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Exceptions\MissingScopeException;

class CheckForAnyScopeMiddleware extends \Laravel\Passport\Http\Middleware\CheckForAnyScope
{
    use UnAuthorizedResponse;

    /**
     * This middleware provide a wrapper arround passport checkForAnyScope middleware by sending a unified
     * unauthorized response
     *
     * {@inheritDoc}
     */
    public function handle($request, $next, ...$scopes)
    {
        try {
            return parent::handle($request, $next, ...$scopes);
        } catch (AuthenticationException $e) {
            return $this->unauthorized($request, $e);
        } catch (AuthorizationException $e) {
            return $this->unauthorized($request, $e);
        } catch (MissingScopeException $e) {
            return $this->unauthorized($request, $e);
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
