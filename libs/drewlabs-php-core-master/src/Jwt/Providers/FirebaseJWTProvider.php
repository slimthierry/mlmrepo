<?php

namespace Drewlabs\Core\Jwt\Providers;

use Drewlabs\Contracts\Jwt\IJWT;
use Drewlabs\Core\Jwt\Exceptions\JwtDecodeException;
use Firebase\JWT\JWT as FirebaseJWT;

class FirebaseJWTProvider implements IJWT
{
    /**
     * Algorithm de hashage du token
     *
     * @var string $alg
     */
    protected $alg;
    /**
     * Clé secret de hashage du token
     *
     * @var string $secret_key
     */
    protected $secret_key;

    public function __construct($alg, $secret_key)
    {
        $this->alg = $alg;
        $this->secret_key = $secret_key;
    }
    /**
     * Converts and signs a PHP object or array into a JWT string.
     *
     * @param object|array  $payload    PHP object or array
     *
     * @return string A signed JWT
     *
     */
    public function encode($payload): ?string
    {
        return $encoded = (string) (FirebaseJWT::encode($payload, $this->secret_key, $this->alg));
    }

    /**
     * Decodes a JWT string into a PHP object.
     *
     * @param string        $jwt            The JWT
     * @param string|array  $secret_key      The key, or map of keys.
     *                                      If the algorithm used is asymmetric, this is the public key
     *
     * @return object The JWT's payload as a PHP object
     */
    public function decode($token): object
    {
        try {
            return $decoded = (object) (FirebaseJWT::decode($token, $this->secret_key, array($this->alg)));
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            throw new JwtDecodeException($e->getMessage());
        } catch (\Firebase\JWT\BeforeValidException $e) {
            throw new JwtDecodeException($e->getMessage());
        } catch (\Firebase\JWT\ExpiredException $e) {
            throw new JwtDecodeException($e->getMessage());
        } catch (\UnexpectedValueException $e) {
            throw new JwtDecodeException($e->getMessage());
        } catch (\Exception $e) {
            throw new JwtDecodeException($e->getMessage());
        }
    }
}
