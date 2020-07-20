<?php

namespace Drewlabs\Core\Database\NoSql\Mongo\Concerns;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;

class MongoBsonTypes
{

    /**
     * Try converting a value to a Mongo BSON Type
     *
     * @param string $type
     * @param string $value
     * @return minxed
     */
    public static function convertToMongoType($type, $value)
    {
        switch (strtolower($type)) {
            case static::BSON_OBJECTID:
                return static::objectID($value);
            case static::BSON_REGEX:
                return static::regex($value);
            case static::BSON_DATE:
                return static::utcDateTime($value);
            default:
                return $value;
        }
    }

    /**
     * Convert the provided $date value to a Mongo BSON UTCDateTime instance
     *
     * @param int|string|mixed $date
     * @return UTCDateTime
     */
    public static function utcDateTime($date = null)
    {
        return self::parseDate($date);
    }

    /**
     * Parse date into mongoDB UTCTimestamps
     *
     * @param mixed $date
     * @return UTCDateTime
     */
    public static function parseDate($date = null)
    {
        // Because PHP time function return timestamps in seconds, need to multiply the returned valu by 1000
        $date = $date ?: time();
        return $date = is_string($date) ? new UTCDateTime((strtotime($date) * 1000)) : new UTCDateTime(($date * 1000));
    }

    /**
     * Convert the provided $value to a Mongo BSON ObjectID instance
     *
     * @param mixed $value
     * @return ObjectId
     */
    public static function objectID($value)
    {
        return new ObjectId($value);
    }

    /**
     * Convert the provided $value to a Mongo BSON Regex instance
     *
     * @param string|mixed $value
     * @return Regex
     */
    public static function regex($value)
    {
        return new Regex($value);
    }
    /**
     * MongoDB BSON objectID string representation
     * @var string
     */
    const BSON_OBJECTID = 'objectid';
    /**
     * MongoDB BSON regular expression string reprensentation
     * @var string
     */
    const BSON_REGEX = 'regex';
    /**
     * MongoDB BSON date string reprensentation
     * @var string
     */
    const BSON_DATE = 'date';
}
