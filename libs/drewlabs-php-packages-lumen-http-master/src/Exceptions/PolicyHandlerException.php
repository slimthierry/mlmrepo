<?php //

namespace Drewlabs\Packages\Http\Exceptions;

class PolicyHandlerException extends \RuntimeException
{
    public function __construct(\Illuminate\Http\Request $request, $message = 'Bad policy handler confuguration error', $code = 500)
    {
        $msg = "Request path : /" . $request->path() . " Error : $message";
        parent::__construct($msg, $code);
    }
}
