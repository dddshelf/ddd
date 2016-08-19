<?php

namespace Ddd\Domain;

class SpySubscriber implements DomainEventSubscriber
{
    public $domainEvent;
    public $isHandled = false;
    private $eventName;

    public function __construct($eventName)
    {
        $this->eventName = $eventName;
    }

    public function isSubscribedTo($aDomainEvent)
    {
        return $this->eventName === $aDomainEvent->name;
    }

    public function handle($aDomainEvent)
    {
        $this->domainEvent = $aDomainEvent;
        $this->isHandled = true;
    }
}
