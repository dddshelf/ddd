<?php

namespace Ddd\Domain\Specification;

/**
 * Interface Specification
 *
 * @package Ddd\Domain\Specification
 */
interface Specification
{
    /**
     * @param mixed $object
     * @return bool
     */
    public function isSatisfiedBy($object);

    /**
     * @param Specification $specification
     * @return Specification
     */
    public function andSpecification(Specification $specification);

    /**
     * @param Specification $specification
     * @return Specification
     */
    public function orSpecification(Specification $specification);

    /**
     * @return Specification
     */
    public function not();
}
