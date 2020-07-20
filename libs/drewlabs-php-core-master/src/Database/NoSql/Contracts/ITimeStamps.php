<?php

namespace Drewlabs\Core\Database\NoSql\Contracts;

interface ITimeStamps
{
    /**
     * Returns the timestamps value keys to apply to each document
     *
     * @return array
     */
    public function getTimeStamps();

    /**
     * Returns the key for creation date of the model
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Returns the key for update date of the model
     *
     * @return string
     */
    public function getUpdatedAt();
}
