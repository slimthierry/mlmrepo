<?php

namespace Drewlabs\Packages\Identity\Http\Response;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait UnAuthorizedResponse
{

    /**
     * Convert an authorization exception into a response.
     *
     * @param  Request  $request
     * @param  \Exception|null  $exception
     * @return Response
     */
    protected function unauthorized($request, \Exception $exception = null)
    {
        $message = $request->method() . ' ' . $request->path() . '  Unauthorized access.' . (isset($exception) ? ' [ERROR] : ' . $exception->getMessage() : '');
        return response($message, 401);
    }
}
