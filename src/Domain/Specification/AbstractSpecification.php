<?php

namespace Ddd\Domain\Specification;

/**
 * Class AbstractSpecification
 *
 * @package Ddd\Domain\Specification
 */
abstract class AbstractSpecification implements Specification
{
    /**
     * @param Specification $specification
     * @return Specification
     */
    public function andSpecification(Specification $specification)
    {
        return new AndSpecification($this, $specification);
    }

    /**
     * @param Specification $specification
     * @return Specification
     */
    public function orSpecification(Specification $specification)
    {
        return new OrSpecification($this, $specification);
    }

    /**
     * @return Specification
     */
    public function not()
    {
        return new NotSpecification($this);
    }
}
