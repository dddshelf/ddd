<?php

namespace Ddd\Domain\EventSourcing;

use Ddd\Domain\DomainEventPublisher;
use Ddd\Domain\FakeDomainEvent;
use Ddd\Domain\SpySubscriber;

class EventSourcingAggregateRootTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SpyEventSourcingAggregateRoot
     */
    private $aggregateRoot;

    /**
     * @var SpySubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->aggregateRoot = new SpyEventSourcingAggregateRoot();
        $this->subscriber    = new SpySubscriber('fake-event');

        DomainEventPublisher::instance()->subscribe($this->subscriber);
    }

    /**
     * @test
     */
    public function itShouldPublishDomainEvent()
    {
        $this->aggregateRoot->someBusinessLogic();

        $this->assertTrue($this->subscriber->isHandled);
    }

    /**
     * @test
     */
    public function itShouldApplyDomainEvent()
    {
        $this->aggregateRoot->someBusinessLogic();

        $this->assertTrue($this->aggregateRoot->isApplied());
    }

    /**
     * @test
     */
    public function itShouldRecordDomainEvent()
    {
        $this->aggregateRoot->someBusinessLogic();

        $this->assertEquals(
            new FakeDomainEvent('fake-event'),
            $this->aggregateRoot->recordedEvents()[0]
        );
    }

    /**
     * @test
     */
    public function itShouldClearRecordedDomainEvent()
    {
        $this->aggregateRoot->someBusinessLogic();
        $this->aggregateRoot->clearRecordedEvents();

        $this->assertEmpty($this->aggregateRoot->recordedEvents());
    }
}

class SpyEventSourcingAggregateRoot
{
    use EventSourcingAggregateRoot;

    /**
     * @var bool
     */
    private $isApplied = false;

    public function someBusinessLogic()
    {
        $this->publishThat(new FakeDomainEvent('fake-event'));
    }

    protected function applyFakeDomainEvent(FakeDomainEvent $domainEvent)
    {
        $this->isApplied = true;
    }

    /**
     * @return boolean
     */
    public function isApplied()
    {
        return $this->isApplied;
    }
}
