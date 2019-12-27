<?php

namespace Ddd\Domain;

class FakeDomainEvent implements DomainEvent
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function occurredOn() {}
}
