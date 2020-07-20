<?php //

namespace Drewlabs\Packages\Http\Exceptions;

class QueryBuilderHandlerException extends \RuntimeException
{
    public function __construct(\Illuminate\Http\Request $request = null, $message = 'Bad query builder confuguration error', $code = 500)
    {
        $msg = isset($request) ? "Request path : /" . $request->path() . " Error : $message" : $message;
        parent::__construct($msg, $code);
    }
}
