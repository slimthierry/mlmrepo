<?php

namespace Drewlabs\Packages\Database\Contracts;

interface TransactionUtils
{
    /**
     * Start a transaction
     *
     * @return void
     */
    public function startTransaction();
    /**
     * Commit a transaction
     *
     * @return void
     */
    public function completeTransaction();
    /**
     * Cancel transaction
     *
     * @return boolean
     */
    public function cancel();
}
