<?php

namespace Drewlabs\Contracts\Http;

interface IHttpClient
{
    /**
     * Send a Http /GET request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return mixed
     */
    public function get($url, $data = array(), $headers = array());

    /**
     * Send a Http /POST request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return mixed
     */
    public function post($url, $data = array(), $headers = array());

    /**
     * Send a Http /PUT request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return mixed
     */
    public function put($url, $data = array(), $headers = array());

    /**
     * Send a Http /DELETE request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     *
     * @return mixed
     */
    public function delete($url, $data = array(), $headers = array());

    /**
     * Set request cookies if any
     *
     * @param array $cookies
     *
     * @return static
     */
    public function setCookies(array $cookies = array());
}
