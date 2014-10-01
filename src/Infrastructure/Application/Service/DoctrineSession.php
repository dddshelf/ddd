<?php

namespace Ddd\Infrastructure\Application\Service;

use Ddd\Application\Service\TransactionalSession;
use Doctrine\ORM\EntityManager;

class DoctrineSession implements TransactionalSession
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function executeAtomically(callable $operation)
    {
        return $this->entityManager->transactional($operation);
    }
}
