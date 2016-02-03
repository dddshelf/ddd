<?php

namespace Ddd\Domain\Specification;

use Ddd\Infrastructure\Domain\Specification\FakeSpecification;

class SpecificationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itShouldSatisfiesSingleSpecification()
    {
        $trueSpec  = new FakeSpecification(true);
        $falseSpec = new FakeSpecification(false);

        $this->assertTrue($trueSpec->isSatisfiedBy(new \StdClass()));
        $this->assertFalse($falseSpec->isSatisfiedBy(new \StdClass()));
    }

    /**
     * @test
     */
    public function itShouldSatisfiesNotSpecificationComposition()
    {
        $trueSpec  = new FakeSpecification(true);
        $falseSpec = new FakeSpecification(false);

        $notTrueSpec  = $trueSpec->not();
        $notFalseSpec = $falseSpec->not();

        $this->assertFalse($notTrueSpec->isSatisfiedBy(new \StdClass()));
        $this->assertTrue($notFalseSpec->isSatisfiedBy(new \StdClass()));
    }

    /**
     * @test
     */
    public function itShouldSatisfiesAndSpecificationComposition()
    {
        $trueSpec  = new FakeSpecification(true);
        $falseSpec = new FakeSpecification(false);

        $trueAndTrueSpec  = $trueSpec->andSpecification($trueSpec);
        $trueAndFalseSpec = $trueSpec->andSpecification($falseSpec);

        $this->assertTrue($trueAndTrueSpec->isSatisfiedBy(new \StdClass()));
        $this->assertFalse($trueAndFalseSpec->isSatisfiedBy(new \StdClass()));
    }

    /**
     * @test
     */
    public function itShouldSatisfiesOrSpecificationComposition()
    {
        $trueSpec  = new FakeSpecification(true);
        $falseSpec = new FakeSpecification(false);

        $trueOrTrueSpec  = $trueSpec->orSpecification($trueSpec);
        $trueOrFalseSpec = $trueSpec->orSpecification($falseSpec);

        $this->assertTrue($trueOrTrueSpec->isSatisfiedBy(new \StdClass()));
        $this->assertTrue($trueOrFalseSpec->isSatisfiedBy(new \StdClass()));
    }
}
