<?php

namespace Drewlabs\Packages\Http\Middleware\Cors;

use Drewlabs\Packages\Http\Middleware\Cors\Contracts\ICorsServices;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsServices implements ICorsServices
{
    /**
     * List of allowed hosts
     *
     * @var array
     */
    protected $allowed_hosts = ['*'];

    /**
     * Access control max age header value
     *
     * @var integer
     */
    protected $max_age = 0;

    protected $allowed_methods = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'OPTIONS',
    ];

    protected $allowed_headers = [
        'X-Requested-With',
        'Content-Type',
        'Accept',
        'Origin',
        'Authorization',
        'Application',
        'Cache-Control',
    ];

    protected $allowed_credentials = true;

    protected $exposed_headers = [];
    /**
     * Current request Request-Headers entries
     *
     * @var string
     */
    public $ac_request_headers = 'Access-Control-Request-Headers';
    /**
     * Current request Request-Methods entries
     *
     * @var string
     */
    public $ac_request_method = 'Access-Control-Request-Method';
    /**
     * Max age of the request headers
     *
     * @var string
     */
    public $ac_max_age_header = 'Access-Control-Max-Age';
    /**
     * Entry for the Allowed methods to be set on the request
     *
     * @var string
     */
    public $ac_allowed_methods_header = 'Access-Control-Allow-Methods';
    /**
     *
     * @var string
     */
    public $ac_allowed_credentials_header = 'Access-Control-Allow-Credentials';
    /**
     * Entry for the Allowed header to be set on the request
     *
     * @var string
     */
    public $ac_allowed_headers_header = 'Access-Control-Allow-Headers';
    /**
     * Entry for the exposed headers to be set on the request
     *
     * @var string
     */
    public $ac_exposed_headers_header = 'Access-Control-Expose-Headers';
    /**
     * Entry for the allowed origins to be set on the request
     *
     * @var string
     */
    public $ac_allowed_origin_header = 'Access-Control-Allow-Origin';

    /**
     * Object initializer
     *
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        if (isset($config) && !empty($config)) {
            $this->setRequiredProperties($config);
        }
    }
    /**
     * Returns whether or not the request is a CORS request.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function isCorsRequest(Request $request)
    {
        return $request->headers->has('Origin');
    }
    /**
     * Returns whether or not the request is a preflight request.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function isPreflightRequest(Request $request)
    {
        return $this->isCorsRequest($request) && $request->isMethod('OPTIONS') && $request->headers->has('Access-Control-Request-Method');
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    protected function setAllowOriginHeaders(Request $request, Response $response): Response
    {
        $origin = $request->headers->get('Origin');
        if (in_array('*', $this->allowed_hosts)) {
            $response->headers->set($this->ac_allowed_origin_header, '*');
        } elseif (Str::is($this->allowed_hosts, $origin)) {
            $response->headers->set($this->ac_allowed_origin_header, $origin);
        }
        return $response;
    }

    /**
     *
     * @inheritDoc
     */
    public function handleRequest(Request $request, Response $response): Response
    {
        if ($this->isPreflightRequest($request)) {
            // Do not set any headers if the origin is not allowed
            if (Str::is($this->allowed_hosts, $request->headers->get('Origin'))) {
                // Set the allowed origin if it is a preflight request
                $response = $this->setAllowOriginHeaders($request, $response);
                $response->headers->set($this->ac_allowed_credentials_header, $this->allowed_credentials ? 'true' : 'false');
                // Set headers max age
                if ($this->max_age) {
                    $response->headers->set($this->ac_max_age_header, (string) $this->max_age);
                }
                // Set the allowed method headers
                $response->headers->set(
                    $this->ac_allowed_methods_header,
                    in_array('*', $this->allowed_methods)
                        ? strtoupper($request->headers->get($this->ac_request_method))
                        : implode(', ', $this->allowed_methods)
                );
                // Set the allowed headers
                $response->headers->set(
                    $this->ac_allowed_headers_header,
                    in_array('*', $this->allowed_headers)
                        ? strtolower($request->headers->get($this->ac_request_headers))
                        : implode(', ', $this->allowed_headers)
                );
            }
            return $response;
        }
        // Do not set any headers if the origin is not allowed
        if (Str::is($this->allowed_hosts, $request->headers->get('Origin'))) {
            $response = $this->setAllowOriginHeaders($request, $response);
            // Set Vary unless all origins are allowed
            if (!in_array('*', $this->allowed_hosts)) {
                $vary = $request->headers->has('Vary') ? $request->headers->get('Vary') . ', Origin' : 'Origin';
                $response->headers->set('Vary', $vary);
            }
            $response->headers->set($this->ac_allowed_credentials_header, $this->allowed_credentials ? 'true' : 'false');
            if (!empty($this->exposed_headers)) {
                $response->headers->set($this->ac_exposed_headers_header, implode(', ', $this->exposed_headers));
            }
        }
        return $response;
    }

    private function setRequiredProperties(array $config)
    {
        $fillables = ['allowed_hosts', 'max_age', 'allowed_headers', 'allowed_headers', 'allowed_credentials', 'exposed_headers'];
        foreach ($fillables as $value) {
            # code...
            if (array_key_exists($value, $config) && !\is_null($config[$value])) {
                if ($value === 'allowed_hosts' && is_array($config[$value])) {
                    $config[$value] = array_map(function ($origin) {
                        if (strpos($origin, '*') !== false) {
                            return $this->convertWildcardToPattern($origin);
                        }
                        return $origin;
                    }, $config[$value]);
                }
                $this->{$value} = $config[$value];
            }
        }
    }

    /**
     * Create a pattern for a wildcard, based on Str::is() from Laravel
     *
     * @param string $pattern
     * @return string
     */
    protected function convertWildcardToPattern($pattern)
    {
        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern);

        return '#^' . $pattern . '\z#u';
    }
}
