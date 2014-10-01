<?php

namespace Ddd\Application\Service;

/**
 * Class TransactionalService
 * @package Ddd\Application\Service
 */
class TransactionalService
{
    /**
     * @var TransactionalSession
     */
    private $session;

    /**
     * @var ApplicationService
     */
    private $service;

    public function __construct(ApplicationService $service, TransactionalSession $session)
    {
        $this->session = $session;
        $this->service = $service;
    }

    public function execute($request)
    {
        if (empty($this->service)) {
            throw new \LogicException('A use case must be specified');
        }

        $operation = function () use ($request) {
            return $this->service->execute($request);
        };

        return $this->session->executeAtomically($operation->bindTo($this));
    }
}
