<?php

namespace Drewlabs\Core\Database\NoSql\Mongo\Concerns;

use Drewlabs\Utils\DateUtils;

trait MongoTimeStampsConcern
{
    /**
     * Convert the Database provider DateTimeUTC Type to PHP Date string
     *
     * @param mixed $date
     * @return void
     */
    public function toDateString($date)
    {
        return date($this->dateStringFormat(), DateUtils::make(static::bsonUtcToDateTime($date))->getTimestamp());
    }

    /**
     * Parse date into Database DateTime Instance
     *
     * @param mixed $date
     * @return \UTCDateTime
     */
    public function toDBDateType($date)
    {
        return static::utcDateTime($date);
    }

    /**
     * Returns the timestamps value keys to apply to each document
     *
     * @return array
     */
    public function getTimeStamps()
    {
        return $time_stamps = static::timeStamps();
    }

    /**
     * Returns the key for creation date of the model
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return static::CREATED_AT;
    }
    /**
     * Returns the key for update date of the model
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return static::UPDATED_AT;
    }

    /**
     * Returns the php string format in which the date is coverted
     *
     * @return string
     */
    private function dateStringFormat()
    {
        return 'Y-m-d H:i:s';
    }
}
