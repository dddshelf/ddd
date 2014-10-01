<?php

namespace Ddd\Application\Service;

/**
 * Interface TransactionalSession
 * @package Ddd\Application\Service
 */
interface TransactionalSession
{
    /**
     * @param  callable $operation
     * @return mixed
     */
    public function executeAtomically(callable $operation);
}
