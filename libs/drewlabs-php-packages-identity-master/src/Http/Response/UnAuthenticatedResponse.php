<?php

namespace Drewlabs\Packages\Identity\Http\Response;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait UnAuthenticatedResponse
{

    /**
     * Convert an authentication exception into a response.
     *
     * @param  Request  $request
     * @param  \Exception|null  $exception
     * @return Response
     */
    protected function unauthenticated($request, \Exception $exception = null)
    {
        $message = $request->method() . ' ' . $request->path() . ' Unauthorized access. Authentication fails' . (isset($exception) ? ' [ERROR] : ' . $exception->getMessage() : '');
        return response($message, 401);
    }

}
