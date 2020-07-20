<?php

namespace Drewlabs\Core\Soap;

/**
 * Simple Wrapper arround PHP SOAP Client
 */
class SoapClientProvider extends \SoapClient
{

 /**
  * SOAP call response
  *
  * @var mixed
  */
    private $soapResponse;

    /**
     * SOAP request headers
     *
     * @var \SoapHeader
     */
    private $headers;

    public function __construct($wsdl = null, array $options = array())
    {
        parent::__construct($wsdl, $options);
    }

    /**
     * Make a new SOAP Provider
     *
     * @param mixed $wsdl
     * @param array $options
     *
     * @return static
     */
    public static function make($wsdl = null, array $options = array())
    {
        $self = new static($wsdl, $options);
        return $self;
    }

    /**
     * Create a new SOAPHeader for the current client
     *
     * @param string $function {Server function}
     * @param \stdClass $params
     * @param string $ns {Service namspace}
     * @param bool $truthy
     *
     * @return static
     */
    public function __makeHeaders($function, \stdClass $params, $ns = null, $truthy = false)
    {
        $header_params = new \SoapVar($params, SOAP_ENC_OBJECT);
        $this->headers = new \SoapHeader($ns, $header_params, $params, $truthy);
        return $this;
    }

    /**
     * Set service headers for a SOAP Client
     *
     * @return static
     */
    public function __setHeaders()
    {
        $this->__setSoapHeaders(array($this->headers));
        return $this;
    }

    /**
     * Call a SOAP server service with given arguments
     *
     * @param string $function
     * @param array $args
     *
     * @return mixed
     */
    public function __callService($function, array $args)
    {
        return $this->__soapCall($function, $args);
    }

    /**
     * Get list of remote procedures available
     *
     * @return array|null
     */
    public function __getServices(): array
    {
        return $this->__getFunctions();
    }

    /**
     * Perform a request to a SOAP service
     *
     * @param string $request_string
     * @param string $location
     * @param string $action_uri
     * @param int $version
     *
     * @return static
     */
    public function __doRequest($request_string, $location, $action_uri, $version, $one_way = null)
    {
        return parent::__doRequest($request_string, $location, null, $version, $one_way);
    }

    /**
     * Get the current SOAP Header
     *
     * @return \SoapHeader
     */
    public function __getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the SOAP service response
     *
     * @return mixed
     */
    public function __response()
    {
        return $this->soapResponse;
    }

    public function __destruct()
    {
        unset($this->soapResponse);
        unset($this->headers);
    }
}
