<?php

namespace Ddd\Infrastructure\Application\Service;

use Ddd\Application\Service\TransactionalSession;

/**
 * Class DummySession
 * @package Ddd\Infrastructure\Application\Service
 */
class DummySession implements TransactionalSession
{
    /**
     * {@inheritDoc}
     */
    public function executeAtomically(callable $operation)
    {
        return call_user_func($operation);
    }
}
