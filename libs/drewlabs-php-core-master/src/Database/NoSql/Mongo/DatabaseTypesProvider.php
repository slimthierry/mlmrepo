<?php

namespace Drewlabs\Core\Database\NoSql\Mongo;

use Drewlabs\Core\Database\NoSql\Contracts\IDatabaseTypes;
use Drewlabs\Core\Database\NoSql\Mongo\Concerns\MongoBsonTypes;
use Drewlabs\Core\Database\NoSql\Mongo\Concerns\MongoTimeStampsConcern as TimeStampsConcern;
use MongoDB\BSON\UTCDateTime;

class DatabaseTypesProvider extends MongoBsonTypes implements IDatabaseTypes
{
    use TimeStampsConcern;

    /**
     * return an array of "created_at" and "updated_at" values
     *
     * @return array
     */
    public static function timeStamps()
    {
        return array(
            self::CREATED_AT => static::utcDateTime(),
            self::UPDATED_AT => static::utcDateTime(),
        );
    }

    /**
     * Covert BSON UTCDateTime to Php \DateTime
     *
     * @param UTCDateTime $date
     * @return \DateTime
     */
    public static function bsonUtcToDateTime(UTCDateTime $date)
    {
        return $date->toDateTime();
    }

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
