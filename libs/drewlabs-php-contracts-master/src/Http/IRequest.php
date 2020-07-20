<?php

namespace Drewlabs\Contracts\Http;

interface IRequest
{
    /**
     * Get the request method
     *
     * @param void
     * @return string
     */
    public function requestMethod(): ?string;

    /**
     * Get the request url
     *
     * @param void
     * @return string
     */
    public function requestUrl(): ?string;

    /**
     * Get the current request path
     *
     * @param void
     * @return string
     */
    public function requestPath(): ?string;

    /**
     * Get the request client Ip Address
     *
     * @return string
     */
    public function requestIp(): ?string;

    /**
     * Get the request body
     *
     * @return array
     */
    public function requestBody(): array;

    /**
     * Get the request host
     *
     * @return string
     */
    public function requestHost(): ?string;

    /**
     * Get the request query parameters
     *
     * @return array
     */
    public function requestQuery(): array;

    /**
     * Get request headers
     *
     * @return array
     */
    public function requestHeaders(): array;

    /**
     * Get a value from the request header
     *
     * @param string $key
     * @return mixed
     */
    public function fromHeaders($key);

    /**
     * Get an input from the request query parameters
     *
     * @param string $key
     * @return mixed
     */
    public function fromQuery($key);

    /**
     * Get an unput from the request body
     *
     * @param string $key
     * @return mixed
     */
    public function fromBody($key);
}
