<?php

namespace Ddd\Infrastructure\PhpUnit;

use Ddd\Application\Service\ApplicationService;
use Ddd\Domain\DomainEvent;
use Ddd\Domain\DomainEventPublisher;

class ApplicationServiceScenarioTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApplicationServiceScenario
     */
    private $scenario;

    public function setUp()
    {
        $this->scenario = new ApplicationServiceScenario(new DummyRepository(), new StubApplicationService());
    }

    /**
     * @test
     */
    public function shouldPublishEvents()
    {
        $this->scenario
            ->given(new \stdClass())
            ->when(new \stdClass())
            ->then(
                [
                    new StubDomainEvent(new \DateTime()),
                ]
            );
    }

    /**
     * @test
     * @expectedException \PHPUnit_Framework_Exception
     */
    public function shouldFailOnMismatchingExpectedNumberOfEvents()
    {
        $this->scenario
            ->given(new \stdClass())
            ->when(new \stdClass())
            ->then([]);
    }

    /**
     * @test
     * @expectedException \PHPUnit_Framework_Exception
     */
    public function shouldFailExpectingWrongEvent()
    {
        $this->scenario
            ->given(new \stdClass())
            ->when(new \stdClass())
            ->then(
                [
                    new AnotherStubDomainEvent(),
                ]
            );
    }

    /**
     * @test
     * @expectedException \PHPUnit_Framework_Exception
     */
    public function shouldFailOnSameExpectedEventWithDifferentValues()
    {
        $this->scenario
            ->given(new \stdClass())
            ->when(new \stdClass())
            ->then(
                [
                    new StubDomainEvent(new \DateTime(), 'bar'),
                ]
            );
    }
}

class StubApplicationService implements ApplicationService
{
    /**
     * @param $request
     */
    public function execute($request = null)
    {
        DomainEventPublisher::instance()->publish(new StubDomainEvent(new \DateTime()));
    }
}

class StubDomainEvent implements DomainEvent
{
    private $occurredOn;

    private $property;

    /**
     * @param \DateTime $occurredOn
     * @param string    $property
     */
    public function __construct(\DateTime $occurredOn, $property = 'foo')
    {
        $this->occurredOn = $occurredOn;
        $this->property = $property;
    }

    /**
     * @return \DateTime
     */
    public function occurredOn()
    {
        return $this->occurredOn;
    }
}

class AnotherStubDomainEvent implements DomainEvent
{
    /**
     * @return \DateTime
     */
    public function occurredOn()
    {
        return new \DateTime();
    }
}

class DummyRepository
{
    /**
     * @param $aggregateRoot
     */
    public function save($aggregateRoot)
    {
        // noop
    }
}
