<?php

namespace Ddd\Domain\Event;

use Ddd\Domain\DomainEvent;

class StoredEvent implements DomainEvent
{
    /**
     * @var int
     */
    private $eventId;

    /**
     * @var string
     */
    private $eventBody;

    /**
     * @var \DateTimeInterface
     */
    private $occurredOn;

    /**
     * @var string
     */
    private $typeName;

    /**
     * @param string $aTypeName
     * @param \DateTimeInterface $anOccurredOn
     * @param string $anEventBody
     */
    public function __construct($aTypeName, \DateTimeInterface $anOccurredOn, $anEventBody)
    {
        $this->eventBody = $anEventBody;
        $this->typeName = $aTypeName;
        $this->occurredOn = $anOccurredOn;
    }

    /**
     * @return string
     */
    public function eventBody()
    {
        return $this->eventBody;
    }

    /**
     * @return int
     */
    public function eventId()
    {
        return $this->eventId;
    }

    /**
     * @return string
     */
    public function typeName()
    {
        return $this->typeName;
    }

    /**
     * @return \DateTimeInterface
     */
    public function occurredOn()
    {
        return $this->occurredOn;
    }
}
