<?php

namespace Ddd\Application\DataTransformer;

class NoTransformationDataTransformer implements DataTransformer
{
    private $object;

    /**
     * @param mixed $object
     */
    public function write($object)
    {
        $this->object = $object;
    }

    /**
     * @return mixed $object
     */
    public function read()
    {
        return $this->object;
    }
}
