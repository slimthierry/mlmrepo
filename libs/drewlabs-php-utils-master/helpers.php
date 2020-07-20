<?php

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('startsWith')) {
    function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}

if (!function_exists('endsWith')) {
    function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
            (substr($haystack, -$length) === $needle);
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('parse_tpl_str')) {

    /**
     * Simple template parsing function for replacing properties|keys of an associative
     * array by their corresponding values
     *
     * Usage:
     * ```
     * $data = array(
     *           "param1" => "Hello",
     *           "param2" => "World",
     *  );
     *  $parsed_str = parse_tpl_str('This is a program just for saying {{$param1}}, to the {{ $param2 }} !!!', $data)
     * ```
     *
     * @param string $str
     * @param array|object $data
     * @return string
     */
    function parse_tpl_str($str, $data)
    {
        if (!is_array($data)) {
            $data = (array) $data;
        }
        $patterns = array();
        $replacements = array();
        // Foreach values in the data attributes
        foreach ($data as $key => $value) {
            # code...
            $patterns[] = '/(\{){2}[ ]?\$' . $key . '[ ]?(\}){2}/i';
            $replacements[] = $value;
        }
        return preg_replace($patterns, $replacements, $str);
    }
}
if (!function_exists('is_lumen')) {
    /**
     * Return the default value of the given value.
     *
     * @param  \stdClass  $value
     * @return mixed
     */
    function is_lumen($callback)
    {
        return (get_class($callback) === "Laravel\Lumen\Application") && preg_match('/(5\.[5-8]\..*)|(6\..*)|(7\..*)/', $callback->version());
    }
}

if (!function_exists('is_assoc')) {
    /**
     * Checks if an array is an associative array
     *
     * @param array $value
     * @return boolean
     */
    function is_assoc(array $value)
    {
        return array_keys($value) !== range(0, count($value) - 1);
    }
}

if (!function_exists('filter_fn_params')) {
    /**
     * Filter paramters of a function based on existance of key in the provided parameter
     *
     * @param string|array $value
     * @return array
     */
    function filter_fn_params($value)
    {
        return function ($param) use ($value) {
            if (is_array($value)) {
                return !in_array($param, $value);
            }
            return $param !== $value;
        };
    }
}

if (!function_exists('build_data_provider')) {

    /**
     * Helper global method for building data provider based on a closure or a class name
     *
     * @param \Closure|string $callback
     * @param array $params
     * @return \Drewlabs\Contracts\Data\IDataProvider
     */
    function build_data_provider($callback, $params = [])
    {
        $provider = null;
        if (is_string($callback) && (\Drewlabs\Utils\Str::contains($callback, ['\\', '\\\\']) === true)) {
            $provider = app($callback);
        }
        if ($callback instanceof \Closure) {
            $provider = $callback(...$params);
        }
        return $provider;
    }
}

if (!function_exists('create_psr_stream')) {

    /**
     * Creates a new Psr Stream object
     *
     * @param \Psr\Http\Message\StreamInterface|ressource|string $ressource
     * @return \Psr\Http\Message\StreamInterface
     */
    function create_psr_stream($ressource)
    {
        if ($ressource instanceof \Psr\Http\Message\StreamInterface) {
            return $ressource;
        }

        if (\is_string($ressource)) {
            $rs = @\fopen('php://temp', 'rw+');
            \fwrite($rs, $ressource);
            $ressource = $rs;
        }
        if (\is_resource($ressource)) {
            return new \GuzzleHttp\Psr7\Stream($ressource);
        }
    }
}

if (!function_exists('convert_size_to_human_readable')) {

    /**
     * Converts bytes into human readable file size.
     *
     * @param string $bytes
     * @return string human readable file size (2,87 Мб)
     * @author Mogilev Arseny
     */
    function convert_size_to_human_readable($bytes, $separator = '.')
    {
        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "unit" => "TB",
                "value" => pow(1024, 4)
            ),
            1 => array(
                "unit" => "GB",
                "value" => pow(1024, 3)
            ),
            2 => array(
                "unit" => "MB",
                "value" => pow(1024, 2)
            ),
            3 => array(
                "unit" => "KB",
                "value" => 1024
            ),
            4 => array(
                "unit" => "B",
                "value" => 1
            ),
        );

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["value"]) {
                $result = $bytes / $arItem["value"];
                $result = str_replace(".", $separator, strval(round($result, 2))) . " " . $arItem["unit"];
                break;
            }
        }
        return $result;
    }
}

if (!function_exists('drewlabs_dispatch_event')) {
    /**
     * Undocumented function
     *
     * @param \Drewlabs\Core\Observable\SubjectProvider $event
     * @param mixed|null $name
     * @return void
     */
    function drewlabs_dispatch_event($event, $params = null)
    {
        return $event->fire($params);
    }
}


if (!function_exists('bloc_component_config')) {
    /**
     * Get configuration values from the drewlabs_application.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function bloc_component_config($key, $default = null)
    {
        $key = 'drewlabs_application.' . $key;
        return \config($key, $default);
    }
}

if (!function_exists('drewlabs_http_handlers_configs')) {
    /**
     * Get configuration values from the drewlabs_http_handlers.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function drewlabs_http_handlers_configs($key, $default = null)
    {
        $key = 'drewlabs_http_handlers.' . $key;
        return \config($key, $default);
    }
}

if (!function_exists('drewlabs_identity_configs')) {
    /**
     * Get configuration values from the drewlabs_identity.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function drewlabs_identity_configs($key, $default = null)
    {
        $key = 'drewlabs_identity.' . $key;
        return \config($key, $default);
    }
}

if (!function_exists('drewlabs_notifications_configs')) {
    /**
     * Get configuration values from the drewlabs_notification.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function drewlabs_notifications_configs($key, $default = null)
    {
        $key = 'drewlabs_notification.' . $key;
        return \config($key, $default);
    }
} // files

if (!function_exists('drewlabs_uploaded_files_configs')) {
    /**
     * Get configuration values from the drewlabs_uploaded_files.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function drewlabs_uploaded_files_configs($key, $default = null)
    {
        $key = 'drewlabs_uploaded_files.' . $key;
        return \config($key, $default);
    }
}

if (!function_exists('drewlabs_utils_configs')) {
    /**
     * Get configuration values from the drewlabs_utils.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function drewlabs_utils_configs($key = null, $default = null)
    {
        $key = 'drewlabs_utils' . trim($key ? ".$key" : '');
        return \config($key, $default);
    }
}

if (!function_exists('drewlabs_passport_configs')) {
    /**
     * Get configuration values from the passport.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function drewlabs_passport_configs($key = null, $default = null)
    {
        $key = 'passport' . trim($key ? ".$key" : '');
        return \config($key, $default);
    }
}

if (!function_exists('drewlabs_workspace_configs')) {
    /**
     * Get configuration values from the drewlabs_workspace.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function drewlabs_workspace_configs($key, $default = null)
    {
        $key = 'drewlabs_workspace.' . $key;
        return \config($key, $default);
    }
} //

if (!function_exists('drewlabs_module_configs')) {
    /**
     * Get configuration values from the drewlabs_module.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function drewlabs_module_configs($key, $default = null)
    {
        $key = 'drewlabs_module.' . $key;
        return \config($key, $default);
    }
}


if (!function_exists('drewlabs_forums_configs')) {
    /**
     * Get configuration values from the drewlabs_forums.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function drewlabs_forums_configs($key, $default = null)
    {
        $key = 'drewlabs_forums.' . $key;
        return \config($key, $default);
    }
}

if (!function_exists('drewlabs_forums_configs')) {
    /**
     * Get configuration values from the drewlabs_forums.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function drewlabs_forums_configs($key, $default = null)
    {
        $key = 'drewlabs_forums.' . $key;
        return \config($key, $default);
    }
}

if (!function_exists('drewlabs_posts_configs')) {
    /**
     * Get configuration values from the drewlabs_posts.php configuration file
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function drewlabs_posts_configs($key, $default = null)
    {
        $key = 'drewlabs_posts.' . $key;
        return \config($key, $default);
    }
}

if (!function_exists('get_illuminate_request_ip')) {
    function get_illuminate_request_ip(\Illuminate\Http\Request $request)
    {
        // Tries getting request from the X-Real-IP header provided by Nginx
        $request_ip = $request->headers->get('X-Real-IP');
        // Call / return the request ip from LARAVEL illuminate ip() method
        return isset($request_ip) ? $request_ip : $request->ip();
    }
}

/**
 * Provides helper for returning a date as a carbon instance
 */
if (!function_exists('as_carbon_instance')) {
    /**
     * Undocumented function
     *
     * @param string|\DateTime|int $date
     * @param string $format
     * @return \Carbon\Carbon
     */
    function as_carbon_instance($date, $format = 'Y-m-d')
    {
        if (!($date instanceof \DateTime) && !is_string($date) && !is_int($date)) {
            throw new \RuntimeException('Invalid date input parameter');
        }
        return $date instanceof \DateTime ? \Carbon\Carbon::instance($date) : (is_string(\Carbon\Carbon::createFromFormat($format, $date)) ?
            \Carbon\Carbon::createFromFormat($format, $date) :
            \Carbon\Carbon::createFromTimestampUTC($date));
    }
}

if (!function_exists('apply_callback_to_paginator_data')) {
    /**
     * Apply data transformation algorithm provided by the callback to each item of the paginator data
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $item
     * @param callable $callback
     * @return \Illuminate\Pagination\AbstractPaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    function apply_callback_to_paginator_data(\Illuminate\Pagination\LengthAwarePaginator $item, callable $callback)
    {
        $transformed = $item
            ->getCollection()
            ->map($callback)->toArray();
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $transformed,
            $item->total(),
            $item->perPage(),
            $item->currentPage(),
            [
                'path' => app(\Illuminate\Http\Request::class)->url(),
                'query' => [
                    'page' => $item->currentPage()
                ]
            ]
        );
    }
}

if (!function_exists('map_query_result')) {
    /**
     * Apply transformation to response object on a get all request
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator|array $item
     * @param callable $callback
     * @return mixed
     */
    function map_query_result($item, callable $callback)
    {
        if (($item instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) || ($item instanceof \Illuminate\Pagination\AbstractPaginator)) {
            return \apply_callback_to_paginator_data($item, $callback);
        }
        $items = isset($item['data']) ? $item['data'] : $item;
        $collection  = is_array($items) ? collect($items) : $items;
        if (!isset($collection)) {
            return [];
        }
        $item['data'] = $collection->map($callback);
        return $item;
    }
}

if (!function_exists('transform_paginator_data')) {
    /**
     * Apply data transformation algorithm provided by the callback to paginator data
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $item
     * @param callable $callback
     * @return \Illuminate\Pagination\AbstractPaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    function transform_paginator_data(\Illuminate\Pagination\LengthAwarePaginator $item, callable $callback)
    {
        $transformed = \call_user_func_array($callback, [$item->getCollection()]);
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $transformed,
            $item->total(),
            $item->perPage(),
            $item->currentPage(),
            [
                'path' => app(\Illuminate\Http\Request::class)->url(),
                'query' => [
                    'page' => $item->currentPage()
                ]
            ]
        );
    }
}

if (!function_exists('transform_query_result')) {
    /**
     * Transform all data by passing them to a user provided callback
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator|array $item
     * @param callable $callback
     * @return mixed
     */
    function transform_query_result($item, callable $callback)
    {
        if (($item instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) || ($item instanceof \Illuminate\Pagination\AbstractPaginator)) {
            return \transform_paginator_data($item, $callback);
        }
        $items = isset($item['data']) ? $item['data'] : $item;
        $collection  = is_array($items) ? collect($items) : $items;
        if (!isset($collection)) {
            return [];
        }
        $item['data'] = \call_user_func_array($callback, [$collection]);
        return $item;
    }
}

if (file_exists(__DIR__ . '/uploaded-files-helpers.php')) {
    require_once __DIR__ . '/uploaded-files-helpers.php';
}

if (file_exists(__DIR__ . '/workspace-helpers.php')) {
    require_once __DIR__ . '/workspace-helpers.php';
}
