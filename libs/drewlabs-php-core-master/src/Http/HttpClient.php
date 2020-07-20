<?php
namespace Drewlabs\Core\Http;

use Drewlabs\Contracts\Http\IHttpClient;

class HttpClient implements IHttpClient
{
    /**
     * The request method
     * @var string
     */
    private $method;

    /**
     * Request headers
     * @var array
     */
    private $headers;

    /**
     * Request key = value pair of data
     *
     * @var array
     */
    private $data;

    /**
     * Request path
     *
     * @var string
     */
    private $path;

    public function __construct()
    {
    }

    /**
     * Static method for building a new request client
     *
     * @param string $path
     * @param string $method
     * @param array $data
     * @param array $header
     *
     * @return static
     */
    public static function make($path, $method = "GET", $data = array(), $headers = array())
    {
        $client = new static();
        $client->method = $method;
        $client->headers = $headers ?: [];
        $client->data = $data;
        $client->path = $path;
        return $client;
    }

    public function request()
    {
        switch ($this->method) {
            case 'GET':
                return $this->get($this->path, $this->data, $this->headers);
            case 'POST':
                return $this->post($this->path, $this->data, $this->headers);
            case 'PUT':
                return $this->put($this->path, $this->data, $this->headers);
            case 'DELETE':
                return $this->delete($this->path, $this->data, $this->headers);
            default:
                throw new \Exception('Unimplemented method');
        }
    }
    /**
     * Send a Http /GET request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return mixed
     */
    public function get($url, $data = array(), $headers = array())
    {
        return;
    }

    /**
     * Send a Http /POST request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return mixed
     */
    public function post($url, $data = array(), $headers = array())
    {
        return;
    }

    /**
     * Send a Http /PUT request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return mixed
     */
    public function put($url, $data = array(), $headers = array())
    {
    }

    /**
     * Send a Http /DELETE request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return mixed
     */
    public function delete($url, $data = array(), $headers = array())
    {
        return;
    }

    /**
     * Set request headers
     *
     * @param array $headers
     *
     * @return static
     */
    protected function setHeaders()
    {
        return $this;
    }

    /**
     * Set request cookies if any
     *
     * @param array $cookies
     *
     * @return static
     */
    public function setCookies(array $cookies = array())
    {
        return $this;
    }

    /**
     * Set request cookies if any
     *
     * @param array $cookies
     *
     * @return static
     */
    public function setUserAgent($agent = '')
    {
        return $this;
    }

    /**
     * Set request referer
     *
     * @param array $referer
     *
     * @return static
     */
    public function setReferer($referer = '')
    {
        return $this;
    }

    /**
     * Set request basic authentication header
     *
     * @param array $referer
     *
     * @return static
     */
    public function setBasicAuth($username, $password)
    {
        return $this;
    }

    /**
     * Add new headers to the class headers property
     *
     * @param $array $headers
     *
     * @return static
     */
    private function mergeHaders(array $headers = array())
    {
        if ($headers) {
            foreach ($headers as $key => $value) {
                # code...
                $this->headers[$key] = $value;
            }
        }
        return $this;
    }
    private function parseCurlResponse()
    {
        return;
    }
}
