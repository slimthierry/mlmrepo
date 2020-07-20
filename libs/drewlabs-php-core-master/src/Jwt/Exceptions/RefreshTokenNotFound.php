<?php

namespace Drewlabs\Core\Jwt\Exceptions;

class RefreshTokenNotFound extends \RuntimeException
{
    public function __construct($message = "Unauthorized access, refresh token not found.", $code = 401)
    {
        parent::__construct($message, $code);
    }
}