<?php

namespace Drewlabs\Core\Database\NoSql\Mongo\Concerns;

use MongoDB\BSON\UTCDateTime;

class MongoTimeStamps
{
    /**
     * return an array of "created_at" and "updated_at" values
     *
     * @return array
     */
    public static function timeStamps()
    {
        return array(
   self::CREATED_AT => MongoBsonTypes::utcDateTime(),
   self::UPDATED_AT => MongoBsonTypes::utcDateTime(),
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
