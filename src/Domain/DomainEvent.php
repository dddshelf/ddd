<?php

namespace Ddd\Domain;

interface DomainEvent
{
    /**
     * @return \DateTimeInterface
     */
    public function occurredOn();
}
