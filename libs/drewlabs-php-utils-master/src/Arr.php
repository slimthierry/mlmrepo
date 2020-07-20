<?php

namespace Drewlabs\Utils;

class Arr
{
    /**
     * Sort a given array using the PHP built-in usort function
     *
     * @param array $items
     * @param \Closure|callable $callback
     *
     * @return array
     */
    public static function sort(array &$items, $callback): array
    {
        \usort($items, $callback);
        return $items;
    }

    /**
     * Process entries in the provided list and return true if the list is a list of list
     *
     * @param array $value
     * @return boolean
     */
    public static function isArrayList(array $value)
    {
        return \array_filter($value, 'is_array') === $value;
    }

    public static function sort_by(array &$items, $by, $order = Comparator::ORD_ASC)
    {
        $compare = function ($a, $b) use ($order, $by) {
            // Check first if is standard type in order to avoid error
            if (Str::is_str($a) || Str::is_str($b)) {
                return Comparator::compare_str($a, $b, $order, $order);
            }
            if (is_numeric($a) || is_numeric($b)) {
                return Comparator::compare_numeric($a, $b, $order);
            }
            // Check if is arrayable
            if (($a instanceof \ArrayAccess || is_array($a)) && ($b instanceof \ArrayAccess || is_array($b))) {
                $a = $a[$by];
                $b = $b[$by];
            }
            // Check if is stdClass type
            if (\is_object($a) && \is_object($b)) {
                $a = $a->{$by};
                $b = $b->{$by};
            }
            if (Str::is_str($a) || Str::is_str($b)) {
                return Comparator::compare_str($a, $b, $order);
            }
            if (is_numeric($a) || is_numeric($b)) {
                return Comparator::compare_numeric($a, $b, $order);
            }
            return $order === Comparator::ORD_DESC ? -1 : 1;
        };
        \usort($items, $compare);
        return $items;
    }

    /**
     * Find index of an array element
     *
     * @param array $items
     * @param string $by
     *
     * @return int|null
     */
    public static function find_index_by(array $items, $by, $search, $start = null, $end = null): int
    {
        $low = isset($start) ? $start : 0;
        $high = isset($end) ? $end : count($items) - 1;

        while ($low <= $high) {
            # code...
            $mid = floor(($low + $high) / 2);
            $searched_item = is_object($items[$mid]) ? $items[$mid]->{$by} : $items[$mid][$by];
            if (Comparator::is_same($searched_item, $search)) {
                return $mid;
            }
            if ($search < $searched_item) {
                $high = $mid - 1;
            } else {
                $low = $mid + 1;
            }
        }
        return -1;
    }

    /**
     * Combine values of two array into a single one
     *
     * @param array $lvalue
     * @param array $rvalue
     *
     * @return array
     */
    public static function combine(array $lvalue, array $rvalue)
    {
        return array_merge($lvalue, $rvalue);
    }

    /**
     * PHP search algorithm wrapper
     * It return the index of the matching elements
     *
     * @param mixed $needle
     * @param array $items
     * @param bool $strict_mode
     *
     * @return int|string|bool
     */
    public static function search($needle, array $items, $strict_mode = false)
    {
        return array_search($needle, $items, $strict_mode);
    }

    /**
     * Filter the array using the given callback.
     *
     * @param  array  $array
     * @param  callable  $callback
     * @return array
     */
    public static function where($array, callable $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Method for swapping two variables
     * @param mixed $lhs
     * @param mixed $rhs
     *
     * @return int
     */
    protected static function swap(&$lhs, &$rhs)
    {
        list($lhs, $rhs) = array($rhs, $lhs);
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int  $key
     * @return bool
     */
    public static function exists($array, $key)
    {
        if ($array instanceof \ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Determine whether the given value is array arrayable.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function arrayable($value)
    {
        return is_array($value) || $value instanceof \ArrayAccess;
    }

    /**
     * Convert a given object to array
     *
     * @param  mixed  $value
     * @return array|null
     */
    public static function objectToArray($value)
    {
        if (static::arrayable($value)) {
            return $value;
        }
        if (is_object($value)) {
            return (array)$value;
        }
        return null;
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|array  $keys
     * @return bool
     */
    public static function has($array, $keys)
    {
        if (is_null($keys)) {
            return false;
        }

        $keys = (array)$keys;

        if (!$array) {
            return false;
        }

        if ($keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $sub_key = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::arrayable($sub_key) && static::exists($sub_key, $segment)) {
                    $sub_key = $sub_key[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (!static::arrayable($array)) {
            return $default instanceof \Closure ? $default() : $default;
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?? ($default instanceof \Closure ? $default() : $default);
        }

        foreach (explode('.', $key) as $segment) {
            if (static::arrayable($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return $default instanceof \Closure ? $default() : $default;
            }
        }

        return $array;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Loop through items and return the result of the callback applied to them
     *
     * @param array $items
     * @param callable $callback
     * @return array
     */
    public static function map($items, callable $callback)
    {
        $keys = array_keys($items);
        $items = array_map($callback, $items, $keys);
        return array_combine($keys, $items);
    }

    /**
     * Loop through a traversable and apply a given callback on each item and return an iterator
     *
     * @param \Traversable $items
     * @param callable $callback
     * @return \Iterator
     */
    public static function iter(\Traversable $items, callable $callback)
    {
        foreach ($items as $value) {
            # code...
            yield \call_user_func($callback, $value);
        }
    }

    /**
     * Checks if a source array contains all the elements of another array
     *
     * @param array $source
     * @param array $innerArray
     * @return boolean
     */
    public static function containsAll(array $source, array $innerArray)
    {
        return count(array_intersect($source, $innerArray)) === count($innerArray);
    }
}
