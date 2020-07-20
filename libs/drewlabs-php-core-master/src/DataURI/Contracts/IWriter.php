<?php

namespace Drewlabs\Core\DataURI\Contracts;


interface IWriter
{
    /**
     * Provide stream write operation definition. Return a boolean value
     * indicating successful write operation
     *
     * @param string $path
     * @param string|mixed $content
     * @param mixed $flags
     * @return boolean
     */
    public function write($path, $content, $flags = null);
}
