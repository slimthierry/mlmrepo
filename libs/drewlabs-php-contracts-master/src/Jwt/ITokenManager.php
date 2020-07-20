<?php

namespace Drewlabs\Contracts\Jwt;

interface ITokenManager
{
    /**
     * Decode a string and return a payload if successful or throws an exception
     * @param string $token
     * @return array|object
     */
    public function decodeToken($token);

    /**
     * Generate a Base64 encoded string containning connected user information, the issuser and validation data
     * @param array|object
     * @throws \RuntimeException;
     * @return static
     */
    public function encodeToken($payload);

    /**
     * Revalidate a Base64 encoded string containning connected user information, the issuser and validation data
     * @param string $token
     * @throws \RuntimeException;
     * @return static
     */
    public function refreshToken($token);

    /**
     * Add a token to the blacklist
     *
     * @param string $token
     * @return bool
     */
    public function invalidateToken($token);
}
