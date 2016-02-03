<?php

namespace Ddd\Domain\Specification;

/**
 * Class NotSpecification
 *
 * @package Ddd\Domain\Specification
 */
class NotSpecification extends AbstractSpecification
{
    /**
     * @var Specification
     */
    private $specification;

    /**
     * @param Specification $specification
     */
    public function __construct(Specification $specification)
    {
        $this->specification = $specification;
    }

    /**
     * @param mixed $object
     * @return bool
     */
    public function isSatisfiedBy($object)
    {
        return !$this->specification->isSatisfiedBy($object);
    }
}
