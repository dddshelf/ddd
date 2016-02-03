<?php

namespace Ddd\Infrastructure\Domain\Specification;

use Ddd\Domain\Specification\AbstractSpecification;

/**
 * Class FakeSpecification
 *
 * @package Ddd\Infrastructure\Domain\Specification
 */
class FakeSpecification extends AbstractSpecification
{
    /**
     * @var bool
     */
    private $bool;

    /**
     * @param boolean $bool
     */
    public function __construct($bool)
    {
        $this->bool = !!$bool;
    }

    /**
     * @param mixed $object
     * @return bool
     */
    public function isSatisfiedBy($object)
    {
        return $this->bool;
    }
}
