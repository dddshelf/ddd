<?php

namespace Ddd\Domain;

class DomainEventPublisherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itShouldNotifySubscriber()
    {
        $this->subscribe($subscriber = new SpySubscriber('test-event'));
        $this->publish($domainEvent = new FakeDomainEvent('test-event'));

        $this->assertEventHandled($subscriber, $domainEvent);
    }

    private function subscribe($subscriber)
    {
        return DomainEventPublisher::instance()->subscribe($subscriber);
    }

    private function publish($domainEvent)
    {
        DomainEventPublisher::instance()->publish($domainEvent);
    }

    private function assertEventHandled($subscriber, $domainEvent)
    {
        $this->assertTrue($subscriber->isHandled);
        $this->assertEquals($domainEvent, $subscriber->domainEvent);
    }

    /**
     * @test
     */
    public function notSubscribedSubscribersShouldNotBeNotified()
    {
        $this->subscribe($subscriber = new SpySubscriber('test-event'));
        $this->publish(new FakeDomainEvent('other-test-event'));

        $this->assertEventNotHandled($subscriber);
    }

    private function assertEventNotHandled($subscriber)
    {
        $this->assertFalse($subscriber->isHandled);
        $this->assertNull($subscriber->domainEvent);
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

    private function unsubscribe($id)
    {
        DomainEventPublisher::instance()->unsubscribe($id);
    }
}
