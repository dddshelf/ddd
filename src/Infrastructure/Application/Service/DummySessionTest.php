<?php

namespace Ddd\Infrastructure\Application\Service;

class DummySessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function methodIsExecuted()
    {
        $expectedValue = new \stdClass();
        $dummySession = new DummySession();

        $this->assertSame(
            $expectedValue,
            $dummySession->executeAtomically(
                function() use ($expectedValue) {
                    return $expectedValue;
                }
            )
        );
    }
}
