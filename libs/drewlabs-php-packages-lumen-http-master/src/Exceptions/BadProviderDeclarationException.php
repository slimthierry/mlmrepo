<?php //

namespace Drewlabs\Packages\Http\Exceptions;

class BadProviderDeclarationException extends \RuntimeException
{
    public function __construct(\Illuminate\Http\Request $request, $message = 'Bad provider configuration definition...', $code = 500)
    {
        $msg = "Request path : /" . $request->path() . " Error : $message";
        parent::__construct($msg, $code);
    }
}
