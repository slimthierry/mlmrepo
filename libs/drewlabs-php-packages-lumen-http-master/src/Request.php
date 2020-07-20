<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Contracts\Http\IRequest;
use Illuminate\Http\Request as BaseRequest;

class Request implements IRequest
{
    /**
     * Framework http request
     *
     * @var BaseRequest
     */
    protected $request;

    /**
     * Request object initialiser
     *
     * @param Request $request
     */
    public function __construct(BaseRequest $request)
    {
        $this->request = $request;
    }
    /**
     * Get the request method
     *
     * @param void
     * @return string
     */
    public function requestMethod(): ?string
    {
        return $this->request->method();
    }

    /**
     * Get the request url
     *
     * @param void
     * @return string
     */
    public function requestUrl(): ?string
    {
        return $this->request->url();
    }

    /**
     * Get the current request path
     *
     * @param void
     * @return string
     */
    public function requestPath(): ?string
    {
        return $this->request->path();
    }

    /**
     * Get the request client Ip Address
     *
     * @return string
     */
    public function requestIp(): ?string
    {
        return $this->request->ip();
    }

    /**
     * Get the request body
     *
     * @return array
     */
    public function requestBody(): array
    {
        return $this->request->all() ?? array();
    }

    /**
     * Get the request host
     *
     * @return string
     */
    public function requestHost(): ?string
    {
        return $this->request->server('HTTP_HOST');
    }

    /**
     * Get the request query parameters
     *
     * @return array
     */
    public function requestQuery(): array
    {
        return $this->request->query() ?? array();
    }

    /**
     * Get request headers
     *
     * @return array
     */
    public function requestHeaders(): array
    {
        return $this->request->headers->all() ?? array();
    }

    /**
     * Get a value from the request header
     *
     * @param string $key
     * @return mixed
     */
    public function fromHeaders($key)
    {
        return is_null($key) ? null : $this->request->header($key);
    }

    /**
     * Get an input from the request query parameters
     *
     * @param string $key
     * @return mixed
     */
    public function fromQuery($key)
    {
        return is_null($key) ? null : $this->request->query->get($key);
    }

    /**
     * Get an unput from the request body
     *
     * @param string $key
     * @return mixed
     */
    public function fromBody($key)
    {
        return is_null($key) ? null : $this->request->input($key);
    }
}
