<?php

namespace Ddd\Domain\EventSourcing;

use Ddd\Domain\DomainEvent;
use Ddd\Domain\DomainEventPublisher;

/**
 * Class EventSourcingAggregateRoot
 */
trait EventSourcingAggregateRoot
{
    /**
     * @var DomainEvent[]
     */
    private $recordedEvents;

    /**
     * @param DomainEvent $event
     */
    protected function publishThat(DomainEvent $event)
    {
        $this->apply($event);
        $this->record($event);
        $this->notify($event);
    }

    /**
     * @param DomainEvent $event
     */
    protected function notify(DomainEvent $event)
    {
        DomainEventPublisher::instance()->publish($event);
    }

    /**
     * @param DomainEvent $event
     */
    protected function record(DomainEvent $event)
    {
        $this->recordedEvents[] = $event;
    }

    /**
     * @param DomainEvent $event
     */
    protected function apply(DomainEvent $event)
    {
        $parts = explode('\\', get_class($event));
        $apply = 'apply' . end($parts);

        $this->$apply($event);
    }

    /**
     * @return DomainEvent[]
     */
    public function recordedEvents()
    {
        return $this->recordedEvents;
    }

    /**
     * @return void
     */
    public function clearRecordedEvents()
    {
        $this->recordedEvents = [];
    }
}
