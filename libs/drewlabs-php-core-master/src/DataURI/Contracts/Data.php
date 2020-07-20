<?php

namespace Drewlabs\Core\DataURI\Contracts;

interface Data
{
    /**
     * Returns the writable content
     *
     * @return string
     */
    public function getRawData();

    /**
     * Set the data content file extension
     *
     * @param string $extension
     * @return static
     */
    public function setExtension($extension);

    /**
     * Return the file extension of the data content
     *
     * @return string
     */
    public function getExtension();

    /**
     * Return the data mimetype
     *
     * @return string
     */
    public function getMimeType();

    /**
     * Returns the paramters binded to the data URI scheme
     *
     * @return mixed[]
     */
    public function getParameters();

    /**
     * Data is binary data
     * @return boolean
     */
    public function isBinaryData();

    // /**
    //  * Set if data is binary data, meaning if it's base64 encoded
    //  *
    //  * @param boolean $boolean
    //  * @return $this
    //  */
    // public function setIsBinaryData($boolean);

    /**
     * Add a custom parameters to the DataURi
     *
     * @param string $paramName
     * @param string $paramValue
     * @return $this
     */
    public function addParameters($paramName, $paramValue);
}
