<?php

namespace Drewlabs\Packages\Http\Exceptions;

class RequestValidationException extends \RuntimeException
{
    public function __construct(\Illuminate\Http\Request $request = null, $message = 'Bad validation configuration error', $code = 500)
    {
        if (isset($request)) {
            $message = "Request path : /" . $request->path() . " Error : $message";
        }
        parent::__construct($message, $code);
    }
}
