<?php

namespace Drewlabs\Contracts\Jwt;

use Drewlabs\Core\Jwt\Exceptions\RefreshTokenExpiredException;
use Drewlabs\Core\Jwt\Exceptions\RefreshTokenNotFound;
use Drewlabs\Contracts\Auth\Authenticatable;

interface IRefreshTokenManager
{
    /**
     * Generate a refresh token from an authenticatable user
     *
     * @param Authenticatable $user
     * @return string
     */
    public function fromUser(Authenticatable $user);

    /**
     * Get the user matching a corresponding user
     *
     * @param string $token
     *
     * @throws RefreshTokenExpiredException
     * @throws RefreshTokenNotFound
     *
     * @return Authenticatable
     */
    public function toUser($token);

    /**
     * This method takes a list of token and set the status value to 1, making then unusable
     *
     * @param array $tokens
     * @return int
     */
    public function invalidateTokens(array $tokens = []);
}
