<?php

namespace Drewlabs\Utils;

class Comparator
{
    const ORD_ASC = 'ASC';
    const ORD_DESC = 'DESC';

    /**
     * Compare two variable of numeric type
     *
     * @param int|float|double $a
     * @param int|float|double $b
     *
     * @return int
     */
    public static function compare_numeric($a, $b, $order)
    {
        return ($order === static::ORD_DESC) ? ($a - $b >= 0 ? 1 : -1) : ($a - $b >= 0 ? -1 : 1);
    }

    /**
     * Compare two variable of string type
     *
     * @param string $a
     * @param string $b
     *
     * @return int
     */
    public static function compare_str($a, $b, $order)
    {
        return ($order === static::ORD_DESC) ? (strcmp($a, $b) >= 0 ? 1 : -1) : (strcmp($a, $b) >= 0 ? -1 : 1);
    }

    /**
     * Verify if two variables are same
     *
     * @param string $a
     * @param string $b
     *
     * @return bool
     */
    public static function is_same($a, $b, $strict = false)
    {
        return $strict ? $a == $b : $a === $b;
    }
}
