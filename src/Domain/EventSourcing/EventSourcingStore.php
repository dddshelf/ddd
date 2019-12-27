<?php

namespace Ddd\Domain\EventSourcing;

use Ddd\Domain\DomainEvent;

interface EventSourcingStore
{
    /**
     * @param string $aggregateId
     * @param int    $skipEvent
     * @param int    $maxCount
     * @return EventStream
     */
    public function loadEventStream($aggregateId, $skipEvent, $maxCount);


    /**
     * @param string        $aggregateId
     * @param DomainEvent[] $events
     * @param int           $expectedVersion
     * @return void
     */
    public function appendToStream($aggregateId, array $events, $expectedVersion);
}
