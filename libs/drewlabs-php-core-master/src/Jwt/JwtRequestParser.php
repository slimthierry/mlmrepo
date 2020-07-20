<?php

namespace Drewlabs\Core\Jwt;

use Drewlabs\Utils\Str;

class JwtRequestParser
{

    /**
     * Parse request and return jwt token
     *
     * @param \Drewlabs\Contracts\Http\IRequest $request
     * @param string $method
     * @param string $header
     * @param string $query
     *
     * @throws \Drewlabs\Core\Jwt\Exceptions\TokenNotFoundException
     *
     * @return string
     */
    public function parse($request, $method = 'bearer', $header = 'authorization', $query = 'token')
    {
        $token = $this->parseAuthHeader($request, $header, $method);
        if ($token) {
            return $token;
        }
        // Try finding the token in the request query parameters
        $token = $request->fromQuery($query);
        if ($token) {
            return $token;
        }
        // Try finding the token in the request body in case of POST request
        $token = $request->fromBody($query);
        if ($token) {
            return $token;
        }
        throw new \Drewlabs\Core\Jwt\Exceptions\TokenNotFoundException('Token key not found');
    }

    /**
     * Parse token from the authorization header.
     *
     * @param \Drewlabs\Contracts\Http\IRequest $request
     * @param string $header
     * @param string $method
     *
     * @return false|string
     */
    public function parseAuthHeader($request, $header = 'authorization', $method = 'bearer')
    {
        $header = $request->fromHeaders($header);
        if (is_null($header)) {
            return false;
        }
        if (!Str::starts_with(strtolower($header), $method)) {
            return false;
        }
        return trim(str_ireplace($method, '', $header));
    }
}
