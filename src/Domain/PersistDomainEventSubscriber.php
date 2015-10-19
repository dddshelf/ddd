<?php

namespace Ddd\Domain;

use Ddd\Domain\Event\PublishableDomainEvent;

class PersistDomainEventSubscriber implements DomainEventSubscriber
{
    /**
     * @var EventStore
     */
    private $eventStore;

    public function __construct($anEventStore)
    {
        $this->eventStore = $anEventStore;
    }

    public function handle($aDomainEvent)
    {
        $this->eventStore->append($aDomainEvent);
    }

    public function isSubscribedTo($aDomainEvent)
    {
        return $aDomainEvent instanceof PublishableDomainEvent;
    }
}
