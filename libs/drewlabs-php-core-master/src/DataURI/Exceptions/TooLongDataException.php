<?php

namespace Drewlabs\Core\DataURI\Exceptions;

/**
 * @package Drewlabs\Core\DataURI
 */
class TooLongDataException extends \RuntimeException
{
    /**
     * @var int
     */
    private $length;

    public function __construct($message, $length)
    {
        parent::__construct($message);
        $this->length = $length;
    }

    /**
     * {@link $length} property getter
     *
     * Returns the maximum supported data length
     *
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }
}
