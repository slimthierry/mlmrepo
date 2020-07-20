<?php

namespace Drewlabs\Packages\PassportPHPLeagueOAuth;

use Laravel\Passport\Passport;
use DateTimeInterface;
use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PassportOAuthUtils
 * @package Drewlabs\Packages\PassportPHPLeagueOAuth
 */
class PassportOAuthUtils
{
    /**
     * Allow simultaneous logins for users
     *
     * @var bool
     */
    public static $allowMultipleTokens = false;
    /**
     * The date when access tokens expire (specific per password client).
     *
     * @var array
     */
    public static $tokensExpireAt = [];

    /**
     * route prefix for passport actions route paths
     *
     * @var string
     */
    public static $routePrefix = 'oauth';

    /**
     * Instruct Passport to keep revoked tokens pruned.
     */
    public static function allowMultipleTokens()
    {
        static::$allowMultipleTokens = true;
    }
    /**
     * Delete older tokens or just mark them as revoked?
     */
    public static function prunePreviousTokens()
    {
        Passport::$pruneRevokedTokens = true;
    }
    /**
     * Get or set when access tokens expire.
     *
     * @param  \DateTimeInterface|null  $date
     * @param int $clientId
     * @return \DateInterval|static
     */
    public static function tokensExpireIn(DateTimeInterface $date = null, $clientId = null)
    {
        if (!$clientId) return Passport::tokensExpireIn($date);
        if (is_null($date)) {
            return isset(static::$tokensExpireAt[$clientId])
                ? Carbon::now()->diff(static::$tokensExpireAt[$clientId])
                : Passport::tokensExpireIn();
        } else {
            static::$tokensExpireAt[$clientId] = $date;
        }
        return new static;
    }

    /**
     * Create a new Psr7 Response object
     *
     * @param integer $code
     * @param string $reasonPhrase
     * @return ResponseInterface
     */
    public static function createPsr7Response(int $code = 200, $reasonPhrase = '')
    {
        return new \GuzzleHttp\Psr7\Response($code);
        // return (new Psr17Factory)->createResponse($code, $reasonPhrase);
    }

    /**
     * Get list of application scopes that will be used in thre current app
     *
     * @return array
     */
    public static function scopes()
    {
        if (!(app('db')
            ->connection()
            ->getSchemaBuilder()
            ->hasTable((new Scope())
                ->getTable()))) {
            return [];
        }
        return Scope::all(['label', 'description_fr', 'description_en'])
            ->map(function ($s) {
                return array($s->label  => $s->description_fr);
            })->flatMap(function ($v) {
                return $v;
            })->all();
    }
}
