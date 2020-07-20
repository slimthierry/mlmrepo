<?php

namespace Drewlabs\Core\DataURI;

/**
 * @package Drewlabs\Core\DataURI
 */
class Writer implements \Drewlabs\Core\DataURI\Contracts\IWriter
{

    /**
     * Data uri writer method
     *
     * @param string $path
     * @param Data $data
     * @return boolean
     */
    public function writeDataURI($path, Data $data, $overwrite = false)
    {
        if ($overwrite && !file_exists($path)) {
            throw new \Drewlabs\Core\DataURI\Exceptions\FileNotFoundException(sprintf('%s file does not exist', $path));
        }
        return $this->write($path, $data->getRawData(), $overwrite ? FILE_APPEND : LOCK_EX);
    }

    /**
     * {@inheritDoc}
     * @throws \Drewlabs\Core\DataURI\Exceptions\FileIOException
     */
    public function write($path, $content, $flags = null)
    {
        $result  = file_put_contents($path, $content, $flags);
        return $result === false ? false : true;
    }
}
