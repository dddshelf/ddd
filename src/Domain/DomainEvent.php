<?php

namespace Ddd\Domain;

interface DomainEvent
{
    /**
     * @return \DateTime
     */
    public function occurredOn();
}
