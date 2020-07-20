<?php

namespace Drewlabs\Core\Database\NoSql\Contracts;

interface IDatabaseTypes extends ITimeStamps
{
    /**
     * Convert the Database provider DateTimeUTC Type to PHP Date string
     *
     * @param mixed $date
     * @return void
     */
    public function toDateString($date);
    /**
     * Parse date into Database DateTime Instance
     *
     * @param mixed $date
     * @return \UTCDateTime
     */
    public function toDBDateType($date);
}
