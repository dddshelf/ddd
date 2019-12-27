<?php

namespace Ddd\Domain\EventSourcing;

use Ddd\Domain\DomainEvent;
use IteratorAggregate;
use ArrayIterator;

/**
 * Class EventStream
 */
class EventStream implements IteratorAggregate
{
    /**
     * @var int
     */
    private $version;

    /**
     * @var DomainEvent[]
     */
    private $events;

    /**
     * @param int           $version
     * @param DomainEvent[] $events
     */
    public function __construct($version, array $events)
    {
        $this->version = $version;
        $this->events  = $events;
    }

    /**
     * @return int
     */
    public function version()
    {
        return $this->version;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->events);
    }
}
