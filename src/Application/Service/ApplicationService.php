<?php

namespace Ddd\Application\Service;

/**
 * Interface ApplicationService
 * @package Ddd\Application\Service
 */
interface ApplicationService
{
    /**
     * @param $request
     * @return mixed
     */
    public function execute($request);
}
