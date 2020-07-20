<?php

namespace Drewlabs\Utils;

class Str
{
    /**
     * Check if a given string starts with a substring
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function starts_with($haystack, $needle)
    {
        return ($needle === "") || (substr($haystack, 0, strlen($needle)) === $needle);
    }

    /**
     * Check if a given string ends with a substring
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function ends_with($haystack, $needle)
    {
        return ($needle === "") || (substr($haystack, -intval(strlen($needle))) === $needle);
    }

    /**
     * Removes some characters from the string
     *
     * @param string $search
     * @param string $str_to_sanitize
     * @param string $replacement
     *
     * @return mixed
     */
    public static function sanitize($search, $str_to_sanitize, $replacement = "")
    {
        return str_replace($search, $replacement, $str_to_sanitize);
    }

    /**
     * Check if a given variable is a string
     *
     * @param string $value
     *
     * @return bool
     */
    public static function is_str($value)
    {
        return is_string($value);
    }

    /**
     * Converts string to lowercase
     *
     * @param string $value
     *
     * @return string
     */
    public static function to_lower_case($value)
    {
        return strtolower($value);
    }

    /**
     * Converts string to uppercase
     *
     * @param string $value
     *
     * @return string
     */
    public static function to_upper_case($value)
    {
        return strtoupper($value);
    }

    /**
     * Converts first character of a string to uppercase
     *
     * @param string $value
     *
     * @return string
     */
    public static function capitalize($value)
    {
        return ucfirst($value);
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needle
     * @return bool
     */
    public static function contains($haystack, $needle)
    {
        // Code patch for searching for string directly without converting it to an array of character
        if (is_string($needle)) {
            return ($needle !== '' && mb_strpos($haystack, $needle) !== false);
        }
        foreach ((array) $needle as $n) {
            if ($n !== '' && mb_strpos($haystack, $n) !== false) {
                return true;
            }
        }

        return false;
    }
    /**
     * Checks if two strings are same
     *
     * @param  string $lhs
     * @param  string $rhs
     * @return bool
     */
    public static function is_same($lhs, $rhs)
    {
        return strcmp($lhs, $rhs) === 0 ? true : false;
    }

    /**
     * Glue together other function parameters with the first {$separator} parameter
     *
     * @param string $separator
     * @param array|mixed ...$values
     * @return void
     */
    public static function concat($separator, ...$values)
    {
        $entries = array_merge([], $values);
        return static::fromArray($entries, $separator);
    }

    /**
     * Glue together items in the provided array using the provided seperator
     *
     * @param array $values
     * @param string $separator
     * @return void
     */
    public static function fromArray(array $values, $delimiter = ',')
    {
        if (!is_array($values)) {
            throw new \RuntimeException('Error parsing value... Provides an array value as parameter');
        }
        return implode($delimiter, $values);
    }

    /**
     * Explode a string variable into an array
     *
     * @param string $value
     * @param string $delimiter
     * @return array
     */
    public static function toArray($value, $delimiter = ',')
    {
        if (!static::is_str($value)) {
            throw new \RuntimeException('Error parsing value... Provides a string value as parameter');
        }
        return $keys = explode($delimiter, $value);
    }

    /**
     * Check if a string endsWith a specific string or character
     *
     * @param string $str
     * @param string $char
     * @return boolean
     */
    public static function endsWith($str, $char)
    {
        return endsWith($str, $char);
    }

    /**
     * Check if a string starts with a specific string or character
     *
     * @param string $str
     * @param string $char
     * @return boolean
     */
    public static function startsWith($str, $char)
    {
        return startsWith($str, $char);
    }

    /**
     * Strip the $char character from the end of the $str string
     *
     * @param string $str
     * @param string|null $char
     * @return string
     */
    public static function rtrim($str, $char = null)
    {
        return rtrim($str, $char);
    }

    /**
     * Strip the $char character from the begin of the $str string
     *
     * @param string $str
     * @param string|null $char
     * @return string
     */
    public static function ltrim($str, $char = null)
    {
        return ltrim($str, $char);
    }

    /**
     * Generate a random string using PHP md5() uniqid() and microtime() functions
     */
    public static function randMd5()
    {
        return md5(uniqid() . microtime());
    }

    /**
     * Replace provided subjects in the provided string
     *
     * @param string $search
     * @param string $replacement
     * @param string|string[] $subject
     * @param int $count
     * @return string|string[]
     */
    public static function replace($search, $replacement, $subject, $count = null)
    {
        return str_replace($search, $replacement, $subject, $count);
    }

    /**
     * Returns the string after the first occurence of the provided character
     *
     * @param string $character
     * @param string $haystack
     * @return string
     */
    public static function after($character, $haystack)
    {
        if (!is_bool(strpos($haystack, $character)))
            return substr($haystack, strpos($haystack, $character) + strlen($character));
    }

    /**
     * Returns the string after the last occurence of the provided character
     *
     * @param string $character
     * @param string $haystack
     * @return string
     */
    public static function after_last($character, $haystack)
    {
        if (!is_bool(static::strrevpos($haystack, $character)))
            return substr($haystack,  static::strrevpos($haystack, $character) + strlen($character));
    }

    /**
     * Returns the string before the first occurence of the provided character
     *
     * @param string $character
     * @param string $haystack
     * @return string
     */
    public static function before($character, $haystack)
    {
        return substr($haystack, 0, strpos($haystack, $character));
    }

    /**
     * Returns the string before the last occurence of the provided character
     *
     * @param string $character
     * @param string $haystack
     * @return string
     */
    public static function before_last($character, $haystack)
    {
        return substr($haystack, 0,  static::strrevpos($haystack, $character));
    }

    /**
     * Returns the string between the first occurence of both provided characters
     *
     * @param string $character
     * @param string $that
     * @param string $haystack
     * @return string
     */
    public static function between($character, $that, $haystack)
    {
        return  static::before($that, static::after($character, $haystack));
    }

    /**
     * Returns the string between the last occurence of both provided characters
     *
     * @param string $character
     * @param string $that
     * @param string $haystack
     * @return string
     */
    public static function between_last($character, $that, $haystack)
    {
        return static::after_last($character, static::before_last($that, $haystack));
    }

    /**
     * Return the provided string in the reverse order
     *
     * @param string $instr
     * @param string $needle
     * @return string
     */
    public static function strrevpos($instr, $needle)
    {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos === false) return false;
        else return strlen($instr) - $rev_pos - strlen($needle);
    }
}
