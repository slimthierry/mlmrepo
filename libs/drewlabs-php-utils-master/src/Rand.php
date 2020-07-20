<?php

namespace Drewlabs\Utils;

/**
 * Random values generator class
 */
class Rand
{

    /**
     * Generate a random api key
     * @param int $length
     * @return string
     */
    public static function appKey($length)
    {
        return str_replace("=", "", str_replace(array(chr(92), "+", chr(47), chr(38)), ".", base64_encode(openssl_random_pseudo_bytes($length))));
    }

    /**
     * @method
     * Generate a new date with added value
     * @param mixed $added_value
     * @param bool $date
     * @return string
     */
    public static function dateTime($added_value, $date = false)
    {
        $timestamp = strtotime($added_value, time());
        return $date === true ? date('Y-m-d', $timestamp) : date('Y-m-d H:i:s', $timestamp);
    }
    /**
     * Generate random string with a specified length
     * @param int $n
     * @return string
     */
    public static function subStr(int $n)
    {
        $characters = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "9", "8", "7", "6", "5", "4", "3", "2", "1", "_", "&", "$", "@", "!", "?", ")", "(", "+"];
        $start = mt_rand(1, \count($characters));
        shuffle($characters);
        $str = implode('', $characters);
        return substr($str, $start, $n);
    }
    /**
     * Simple function for generating random password
     * @param int $it
     * @return string
     */
    public static function password(int $it = 4)
    {
        $tmpstr = '';
        for ($i = $it; $i > 0; $i--) {
            $tmpstr .= static::subStr($i);
        }
        return $tmpstr;
    }
    /**
     * Generate a random integer between a minimum and a maximum values
     * @param int $min
     * @param int $max
     * @return int
     */
    public static function int(int $min, int $max)
    {
        return function_exists('random_int') ? random_int($min, $max) : mt_rand($min, $max);
    }

    /**
     * Generate a Global unique identifier (GUID)
     * @return string
     */
    public static function guid()
    {
        if (function_exists('com_create_guid')) {
            return \trim(\com_create_guid(), '{}');
        }
        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }
}
