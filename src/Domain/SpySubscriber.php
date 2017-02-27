<?php

namespace Ddd\Domain;

class SpySubscriber implements DomainEventSubscriber
{
    private $domainEvents = [];

    /**
     * @var bool
     */
    private $shouldSubscribeToEvents;

    /**
     * @param bool $shouldSubscribeToEvents
     */
    public function __construct($shouldSubscribeToEvents = true)
    {
        $this->shouldSubscribeToEvents = $shouldSubscribeToEvents;
    }

    /**
     * @param DomainEvent $aDomainEvent
     *
     * @return bool
     */
    public function isSubscribedTo($aDomainEvent)
    {
        return $this->shouldSubscribeToEvents;
    }

    /**
     * @param DomainEvent $aDomainEvent
     */
    public function handle($aDomainEvent)
    {
        $this->domainEvents[] = $aDomainEvent;
    }

    /**
     * @return DomainEvent[]
     */
    public function domainEvents()
    {
        return $this->domainEvents;
    }

    /**
     * @return DomainEvent
     */
    public function mostRecentEvent()
    {
        $allEvents = $this->domainEvents();

        return end($allEvents);
    }
}
