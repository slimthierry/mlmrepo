<?php //

namespace Drewlabs\Packages\Http\Exceptions;

class TransformRequestBodyException extends \RuntimeException
{
    public function __construct(\Illuminate\Http\Request $request = null, $message = 'Bad transform request configuration error', $code = 500)
    {
        if (isset($request)) {
            $message = "Request path : /" . $request->path() . " Error : $message";
        }
        parent::__construct($message, $code);
    }
}
