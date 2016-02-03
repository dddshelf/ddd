<?php

namespace Ddd\Domain\Specification;

/**
 * Class AndSpecification
 *
 * @package Ddd\Domain\Specification
 */
class AndSpecification extends AbstractSpecification
{
    /**
     * @var Specification
     */
    private $one;

    /**
     * @var Specification
     */
    private $other;

    /**
     * @param Specification $one
     * @param Specification $other
     */
    public function __construct(Specification $one, Specification $other)
    {
        $this->one   = $one;
        $this->other = $other;
    }

    /**
     * @param mixed $object
     * @return bool
     */
    public function isSatisfiedBy($object)
    {
        return $this->one->isSatisfiedBy($object) && $this->other->isSatisfiedBy($object);
    }
}
