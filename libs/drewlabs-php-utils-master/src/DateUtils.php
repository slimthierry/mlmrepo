<?php

namespace Drewlabs\Utils;

class DateUtils extends \DateTime
{
    /**
     * Number of X in Y.
     */
    const YEARS_PER_CENTURY = 100;
    const YEARS_PER_DECADE = 10;
    const MONTHS_PER_YEAR = 12;
    const MONTHS_PER_QUARTER = 3;
    const WEEKS_PER_YEAR = 52;
    const WEEKS_PER_MONTH = 4;
    const DAYS_PER_WEEK = 7;
    const HOURS_PER_DAY = 24;
    const MINUTES_PER_HOUR = 60;
    const SECONDS_PER_MINUTE = 60;

    /**
     * RFC7231 DateTime format.
     *
     * @var string
     */
    const RFC7231_FORMAT = 'D, d M Y H:i:s \G\M\T';

    /**
     * Default format to use for __toString method when type juggling occurs.
     *
     * @var string
     */
    const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s';

    /**
     * Format for converting mocked time, includes microseconds.
     *
     * @var string
     */
    const MOCK_DATETIME_FORMAT = 'Y-m-d H:i:s.u';
    /**
     * Create a DateUtils from a DateTime.
     *
     * @param \DateTime|\DateTimeInterface $date
     *
     * @return static
     */
    public static function make($date)
    {
        if ($date instanceof static) {
            return clone $date;
        }

        static::expect_date_time($date);

        return new static($date->format('Y-m-d H:i:s.u'), $date->getTimezone());
    }

    /**
     * Get an instance of the DateUtils for the current date and time.
     *
     * @param \DateTimeZone|string|null $time_zone
     *
     * @return static
     */
    public static function now($time_zone = null)
    {
        return new static(null, $time_zone);
    }

    /**
     * Add minutes to the \DateTime instance
     *
     * @param int $added_value
     *
     * @return static
     */
    public function add_minutes($added_value = 0)
    {
        return $this->modify((int) $added_value . ' minute');
    }

    /**
     * Create an instance of the DateUtils from an UTC timestamp.
     *
     * @param int $timestamp
     *
     * @return static
     */
    public static function from_timestamp($timestamp)
    {
        return new static('@' . $timestamp);
    }

    /**
     * Compares the formatted values of the two dates.
     *
     * @param string                                 $format The date formats to compare.
     * @param \Drewlabs\Utils\DateUtils|\DateTimeInterface|null $date   The instance to compare with or null to use current day.
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function is_same($format, $date = null): bool
    {
        $date = $date ?: static::now($this->tz);

        static::expect_date_time($date, 'null');

        return $this->format($format) === $date->format($format);
    }

    /**
     * Determines if the instance is in the past, ie. less (before) than now.
     *
     * @return bool
     */
    public function is_past(): bool
    {
        return $this->less_than($this->now_with_tz());
    }

    /**
     * Determines if the instance is in the future, ie. less (before) than now.
     *
     * @return bool
     */
    public function is_future(): bool
    {
        return $this->greater_than($this->now_with_tz());
    }

    /**
     * Returns a present instance in the same timezone.
     *
     * @return static
     */
    public function now_with_tz()
    {
        return static::now($this->getTimezone());
    }

    /**
     * Determines if the instance is less (before) than another
     *
     * @param \Drewlabs\Utils\DateUtils|\DateTimeInterface|mixed $date
     *
     * @return bool
     */
    public function less_than($date): bool
    {
        return $this < $date;
    }

    /**
     * Determines if the instance is greater (after) than another
     *
     * @param \Drewlabs\Utils\DateUtils|\DateTimeInterface|mixed $date
     *
     * @return bool
     */
    public function greater_than($date): bool
    {
        return $this > $date;
    }

    /**
     * Get the maximum instance between a given instance (default now) and the current instance.
     *
     * @param \Drewlabs\Utils\DateUtils|\DateTimeInterface|string|null $date
     *
     * @return static
     */
    public function max($date = null)
    {
        $date = $this->resolve($date);

        return $this->greater_than($date) ? $this : $date;
    }

    /**
     * Return the Carbon instance passed through, a now instance in the same timezone
     * if null given or parse the input if string given.
     *
     * @param \Drewlabs\Utils\DateUtils|\DateTimeInterface|string|null $date
     *
     * @return static
     */
    protected function resolve($date = null)
    {
        if (!$date) {
            return $this->now_with_tz();
        }

        if (is_string($date)) {
            return new static($date, $this->getTimezone());
        }

        static::expect_date_time($date, array('null', 'String'));

        return $date instanceof self ? $date : static::make($date);
    }

    /**
     * Throws an exception if the given object is not a DateTime and does not implement DateTimeInterface
     * and not in $other.
     *
     * @param mixed        $date
     * @param string|array $other
     *
     * @throws \InvalidArgumentException
     */
    protected static function expect_date_time($date, $other = array())
    {
        $message = 'Expected type : ';
        foreach ((array) $other as $expect) {
            $message .= "{$expect}, ";
        }

        if (!$date instanceof \DateTime && !$date instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException(
       $message . 'DateTime or DateTimeInterface, ' . (is_object($date) ? get_class($date) : gettype($date)) . ' given'
   );
        }
    }

    /**
     * Get the difference in hours.
     *
     * @param \Drewlabs\Utils\DateUtils|\DateTimeInterface|string|null $date
     * @param bool $exact Get the exact of the difference
     *
     * @return int
     */
    public function hrs_diff($date = null, $exact = true): int
    {
        return (int) ($this->secs_diff($date, $exact) / static::SECONDS_PER_MINUTE / static::MINUTES_PER_HOUR);
    }

    /**
     * Get the difference in minutes.
     *
     * @param \Drewlabs\Utils\DateUtils|\DateTimeInterface|string|null $date
     * @param bool $exact Get the exact of the difference
     *
     * @return int
     */
    public function min_diff($date = null, $exact = true): int
    {
        return (int) ($this->secs_diff($date, $exact) / static::SECONDS_PER_MINUTE);
    }

    /**
     * Get the difference in seconds.
     *
     * @param \Drewlabs\Utils\DateUtils|\DateTimeInterface|string|null $date
     * @param bool $exact Get the exact of the difference
     *
     * @return int
     */
    public function secs_diff($date = null, $exact = true): int
    {
        $diff = $this->diff($this->resolve($date));
        $value = $diff->days * static::HOURS_PER_DAY * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE +
  $diff->h * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE +
  $diff->i * static::SECONDS_PER_MINUTE +
  $diff->s;

        return $exact || !$diff->invert ? $value : -$value;
    }
}
