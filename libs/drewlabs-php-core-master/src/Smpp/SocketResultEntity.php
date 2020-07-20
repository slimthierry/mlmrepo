<?php

namespace Drewlabs\Core\Smpp;

class SocketResultEntity
{
    /**
     * Sms result status code
     *
     * @var int
     */
    public $status_code;

    /**
     * Sms result message
     *
     * @var string
     */
    public $message;

    /**
     * The identifier value of the current message
     *
     * @var mixed
     */
    public $id;

    /**
     * SmsResultEntity instance initializer
     *
     * @param mixed $status
     * @param string $message
     */
    public function __construct($id, int $status, $message)
    {
        $this->id = $id;
        $this->status_code = $status;
        $this->message = $message;
    }
}
