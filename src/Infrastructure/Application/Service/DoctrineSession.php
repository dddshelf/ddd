<?php

namespace Ddd\Infrastructure\Application\Service;

use Ddd\Application\Service\TransactionalSession;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DoctrineSession
 * @package Ddd\Infrastructure\Application\Service
 */
class DoctrineSession implements TransactionalSession
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
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
