<?php

namespace Drewlabs\Core\Jwt\Exceptions;

class RefreshTokenExpiredException extends \RuntimeException
{
    public function __construct($message = "Unauthorized access, refresh token has expired", $code = 401)
    {
        parent::__construct($message, $code);
    }
}
