<?php

namespace Drewlabs\Core\DataURI;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException as SymfonyFileException;

/**
 * @package Drewlabs\Core\DataURI
 *
 * This class represent URI encoded data object. It holds information
 * such as mediaType, data and mime
 */
class Data implements \Drewlabs\Core\DataURI\Contracts\Data
{
    /**
     *
     */
    const LITLEN = 0;

    /**
     * The ATTSPLEN (2100) limits the sum of all
     * lengths of all attribute value specifications which appear in a tag
     */
    const ATTSPLEN = 1;

    /**
     * The TAGLEN (2100) limits the overall length of a tag
     */
    const TAGLEN = 2;

    /**
     * ATTS_TAG_LIMIT is the length limit allowed for TAGLEN & ATTSPLEN DataURi
     */
    const ATTS_TAG_LIMIT = 2100;

    /**
     * LIT_LIMIT is the length limit allowed for LITLEN DataURi
     */
    const LIT_LIMIT = 1024;

    /**
     * Extension indicating that the data URI is base64 encoded
     */
    const BASE_64 = 'base64';

    /**
     * @var string
     */
    private $data;

    /**
     * @var string
     */
    private $mimeType;

    /**
     *
     * @var string
     */
    private $extension;

    /**
     * Parameters provided in DataURI
     *
     * @var Array
     */
    private $parameters;

    /**
     * @var boolean
     */
    private $isBinaryData = false;

    /**
     * A DataURI Object which by default has a 'text/plain'
     * media type and a 'charset=US-ASCII' as optional parameter
     *
     * @param string    $data       Data to include as "immediate" data
     * @param string    $mimeType   Mime type of media
     * @param array     $parameters Array of optional parameters
     * @param boolean   $strict     Check length of data
     * @param int       $lengthMode Define Length of data
     */
    public function __construct(
        $data,
        $mimeType = null,
        array $parameters = array(),
        $strict = false,
        $lengthMode = self::TAGLEN
    ) {
        $this->data = $data;
        $this->mimeType = $mimeType;
        $this->parameters = $parameters;

        $this->buildAttributes($lengthMode, $strict);
    }

    /**
     * @inheritDoc
     */
    public function getRawData()
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * {@inheritDoc}
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @inheritDoc
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @inheritDoc
     */
    public function isBinaryData()
    {
        return $this->isBinaryData;
    }

    /**
     * @inheritDoc
     */
    public function setIsBinaryData($boolean)
    {
        $this->isBinaryData = (boolean) $boolean;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addParameters($paramName, $paramValue)
    {
        $this->parameters[$paramName] = $paramValue;
        return $this;
    }

    /**
     * Get a new instance of DataUri\Data from a file
     *
     * @param string $file Path to the located file
     * @param boolean $strict Use strict mode
     * @param int $lengthMode The length mode
     * @return Data
     * @throws \Drewlabs\Core\DataURI\Exceptions\FileNotFoundException
     */
    public static function fromFilePath($file, $strict = false, $lengthMode = Data::TAGLEN)
    {
        if ( ! $file instanceof \Symfony\Component\HttpFoundation\File\File) {
            try {
                $file = new \Symfony\Component\HttpFoundation\File\File($file);
            } catch (SymfonyFileException $e){
                throw new \Drewlabs\Core\DataURI\Exceptions\FileNotFoundException(sprintf('%s file does not exist', $file));
            }
        }
        $data = file_get_contents($file->getPathname());
        return new static($data, $file->getMimeType(), array(), $strict, $lengthMode);
    }

    /**
     * Create an instance of the Drewlabs\Core\DataURI\Data class from a user provided url
     *
     * @param string $url Path to the remote file
     * @param boolean $strict Use strict mode
     * @param int $lengthMode The length mode
     * @return static
     * @throws \Drewlabs\Core\DataURI\Exceptions\FileNotFoundException
     */
    public static function fromURL($url, $strict = false, $lengthMode = Data::TAGLEN)
    {
        if (! extension_loaded('curl')) {
            throw new \RuntimeException('This method requires the CURL extension.');
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            curl_close($ch);
            throw new \Drewlabs\Core\DataURI\Exceptions\FileNotFoundException(sprintf('%s file does not exist or the remote server does not respond', $url));
        }
        $mimeType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        return new static($data, $mimeType, array(), $strict, $lengthMode);
    }

    /**
     *
     * @param int       $lengthMode     Max allowed data length
     * @param boolean   $strict         Check data length
     * @throws \Drewlabs\Core\DataURI\Exceptions\TooLongDataException
     * @return static
     */
    private function buildAttributes($lengthMode, $strict)
    {
        if ($strict && $lengthMode === self::LITLEN && strlen($this->data) > self::LIT_LIMIT) {
            throw new \Drewlabs\Core\DataURI\Exceptions\TooLongDataException('Too long data', strlen($this->data));
        } elseif ($strict && strlen($this->data) > self::ATTS_TAG_LIMIT) {
            throw new \Drewlabs\Core\DataURI\Exceptions\TooLongDataException('Too long data', strlen($this->data));
        }
        if (null === $this->mimeType) {
            $this->mimeType = 'text/plain';
            $this->addParameters('charset', 'US-ASCII');
        }
        $this->isBinaryData = strpos($this->mimeType, 'text/') !== 0;
        return $this;
    }

    /**
     * String dumper of the current class
     *
     * @return string
     */
    public function __toString()
    {
        $parameters = '';
        if (0 !== count($params = $this->getParameters())) {
            foreach ($params as $paramName => $paramValue) {
                $parameters .= sprintf(';%s=%s', $paramName, $paramValue);
            }
        }
        $base64 = '';
        if($this->isBinaryData()){
            $base64 = sprintf(';%s', Data::BASE_64);
            $data = base64_encode($this->getRawData());
        }else{
            $data = rawurlencode($this->getRawData());
        }
        return sprintf('data:%s%s%s,%s'
                , $this->getMimeType()
                , $parameters
                , $base64
                , $data
        );
    }
}
