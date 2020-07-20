<?php

use Illuminate\Database\DatabaseManager;

if ( ! function_exists('config_path'))
{
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

if (! function_exists('get_db_connection'))
{
    /**
     * Get the database connection provider
     *
     * @return DatabaseManager
     */
    function get_db_connection()
    {
        return app()['db'];
    }
}

if(! function_exists('startsWith')) {
    /**
     *
     * @param string $haystack
     * @param string $needle
     * @return boolean
     */
    function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}

if(! function_exists('endsWith')){
    /**
     *
     * @param string $haystack
     * @param string $needle
     * @return boolean
     */
    function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
        (substr($haystack, -$length) === $needle);
    }
}
