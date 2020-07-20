<?php

namespace Drewlabs\Packages\Database;

use Drewlabs\Packages\Database\Contracts\TransactionUtils as ContractsTransactionUtils;

class DataTransactionUtils implements ContractsTransactionUtils
{

    /**
     * Database utilities provider
     *
     * @param Container $app
     */
    public function __construct()
    {
    }
    /**
     * Start a data inserting transaction
     *
     * @return void
     */
    public function startTransaction()
    {
        app()['db']->beginTransaction();
    }
    /**
     * Commit a data inserting transaction
     *
     * @return void
     */
    public function completeTransaction()
    {
        app()['db']->commit();
    }
    /**
     * Cancel a data insertion transaction
     *
     * @return boolean
     */
    public function cancel()
    {
        app()['db']->rollback();
    }
}
