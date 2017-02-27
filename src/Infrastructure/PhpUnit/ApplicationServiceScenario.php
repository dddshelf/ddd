<?php

namespace Ddd\Infrastructure\PhpUnit;

use Ddd\Application\Service\ApplicationService;
use Ddd\Domain\DomainEvent;
use Ddd\Domain\DomainEventPublisher;
use Ddd\Domain\SpySubscriber;
use PHPUnit_Framework_Assert as PHPUnitAssert;

class ApplicationServiceScenario
{
    /**
     * @var ApplicationService
     */
    private $applicationService;

    private $repository;

    /**
     * @var SpySubscriber
     */
    private $spy;

    /**
     * @param                    $repository
     * @param ApplicationService $applicationService
     */
    public function __construct($repository, ApplicationService $applicationService)
    {
        $this->repository = $repository;
        $this->applicationService = $applicationService;

        $this->spy = new SpySubscriber();
    }

    /**
     * @param $aggregateRoot
     *
     * @return $this
     */
    public function given($aggregateRoot)
    {
        $this->repository->save($aggregateRoot);

        return $this;
    }

    /**
     * @param $request
     *
     * @return $this
     */
    public function when($request)
    {
        DomainEventPublisher::instance()->subscribe($this->spy);
        $this->applicationService->execute($request);

        return $this;
    }

    /**
     * @param DomainEvent[] $events
     */
    public function then(array $events)
    {
        PHPUnitAssert::assertCount(count($events), $this->spy->domainEvents());

        $iterator = new \MultipleIterator(\MultipleIterator::MIT_NEED_ALL);
        $iterator->attachIterator(new \ArrayIterator($events));
        $iterator->attachIterator(new \ArrayIterator($this->spy->domainEvents()));

        foreach ($iterator as $bothEvents) {
            $this->assertEqualEvents($bothEvents[0], $bothEvents[1]);
        }
    }

    /**
     * @param DomainEvent $anEvent
     * @param DomainEvent $anotherEvent
     */
    private function assertEqualEvents(DomainEvent $anEvent, DomainEvent $anotherEvent)
    {
        PHPUnitAssert::assertEquals(get_class($anEvent), get_class($anotherEvent));
        PHPUnitAssert::assertEquals(
            $this->varsWithoutOccurredOnOf($anEvent),
            $this->varsWithoutOccurredOnOf($anotherEvent)
        );
        PHPUnitAssert::assertEquals($anEvent->occurredOn(), $anotherEvent->occurredOn(), 'OccurredOn not equal', 5);
    }

    /**
     * @param DomainEvent $event
     *
     * @return array
     */
    private function varsWithoutOccurredOnOf(DomainEvent $event)
    {
        $getObjectVars = function () {
            $vars = get_object_vars($this);
            $vars = array_diff_key($vars, array_flip(['occurredOn']));

            return $vars;
        };

        $getObjectVars = $getObjectVars->bindTo($event, get_class($event));

        return $getObjectVars();
    }
}
