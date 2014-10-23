<?php

namespace Ddd\Infrastructure\Application\Service;

use Ddd\Application\Service\TransactionalSession;
use ADOConnection;
use Exception;

class AdodbSession implements TransactionalSession
{
    /**
     * @var ADOConnection
     */
    private $connection;

    public function executeAtomically(callable $operation)
    {
        $this->connection->StartTrans();

        try {
            $outcome = $operation();

            $this->connection->CommitTrans();

            return $outcome;
        } catch (Exception $e) {
            $this->connection->RollbackTrans();
            throw $e;
        }
    }
}