<?php

namespace Ddd\Infrastructure\PhpUnit;

use Ddd\Application\Service\ApplicationService;

abstract class AbstractApplicationServiceScenario extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApplicationServiceScenario
     */
    protected $scenario;

    public function setUp()
    {
        $repository     = $this->aggregateRootRepository();
        $this->scenario = new ApplicationServiceScenario(
            $repository,
            $this->applicationService($repository)
        );
    }

    /**
     * @param $repository
     *
     * @return ApplicationService
     */
    abstract public function applicationService($repository);

    /**
     * @return mixed Repository
     */
    abstract public function aggregateRootRepository();
}
