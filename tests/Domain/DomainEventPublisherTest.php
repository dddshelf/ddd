<?php

namespace Ddd\Domain;

class DomainEventPublisherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itShouldNotifySubscriber()
    {
        $this->subscribe($subscriber = new SpySubscriber());
        $this->publish($domainEvent = new FakeDomainEvent('test-event'));

        $this->assertEventHandled($subscriber, $domainEvent);
    }

    /**
     * @param $subscriber
     *
     * @return int
     */
    private function subscribe($subscriber)
    {
        return DomainEventPublisher::instance()->subscribe($subscriber);
    }

    /**
     * @param DomainEvent $domainEvent
     */
    private function publish(DomainEvent $domainEvent)
    {
        DomainEventPublisher::instance()->publish($domainEvent);
    }

    /**
     * @param SpySubscriber $subscriber
     * @param DomainEvent   $domainEvent
     */
    private function assertEventHandled(SpySubscriber $subscriber, DomainEvent $domainEvent)
    {
        $this->assertTrue(in_array($domainEvent, $subscriber->domainEvents()));
        $this->assertEquals($domainEvent, $subscriber->mostRecentEvent());
    }

    /**
     * @test
     */
    public function notSubscribedSubscribersShouldNotBeNotified()
    {
        $shouldSubscribeToEvents = false;
        $this->subscribe($subscriber = new SpySubscriber($shouldSubscribeToEvents));
        $this->publish(new FakeDomainEvent('other-test-event'));

        $this->assertEventNotHandled($subscriber);
    }

    /**
     * @param SpySubscriber $subscriber
     */
    private function assertEventNotHandled(SpySubscriber $subscriber)
    {
        $this->assertEmpty($subscriber->domainEvents());
    }

    /**
     * @test
     */
    public function itShouldUnsubscribeSubscriber()
    {
        $subscriberId = $this->subscribe($subscriber = new SpySubscriber('test-event'));
        $this->unsubscribe($subscriberId);
        $this->publish(new FakeDomainEvent('test-event'));

        $this->assertEventNotHandled($subscriber);
    }

    /**
     * @param $id
     */
    private function unsubscribe($id)
    {
        DomainEventPublisher::instance()->unsubscribe($id);
    }
}


class FakeDomainEvent implements DomainEvent
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function occurredOn() {}
}
