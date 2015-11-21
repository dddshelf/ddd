<?php

namespace Ddd\Test\Infrastructure\Application\Notification;

use DateTime;
use Ddd\Domain\DomainEvent;
use Ddd\Domain\Event\StoredEvent;
use Ddd\Infrastructure\Application\Notification\AmqpExchangeListener;
use JMS\Serializer\SerializerBuilder;
use PHPUnit_Framework_TestCase;
use React\EventLoop\Factory;
use stdClass;

class AmqpExchangeListenerTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldListenForIncomingMessagesInAQueue()
    {
        $queue = $this->prophesize('AMQPQueue');

        $serializer =
            SerializerBuilder::create()
                ->setCacheDir(__DIR__ . '/../../../../../var/cache/jms-serializer')
                ->addMetadataDir(__DIR__ . '/../../../../../src/Infrastructure/Application/Serialization/JMS/Config')
            ->build();

        $storedEvent = new StoredEvent(
            'Ddd\Test\Infrastructure\Application\Notification\TestEvent',
            new DateTime(),
            $serializer->serialize(new TestEvent(), 'json')
        );

        $envelope = $this->prophesize('AMQPEnvelope');

        $envelope->getBody()->willReturn($serializer->serialize($storedEvent, 'json'));

        $revealedEnvelope = $envelope->reveal();
        $haveBeenCalled = false;

        $queue->get()->will(function() use ($revealedEnvelope, &$haveBeenCalled) {
            if (false === $haveBeenCalled) {
                $haveBeenCalled = true;
                return $revealedEnvelope;
            }

            return false;
        });

        $loop = Factory::create();

        $listener = new TestListener(
            $queue->reveal(),
            $loop,
            $serializer
        );

        sleep(1);

        $loop->tick();

        $this->assertCount(1, $listener->receivedEvents());
    }
}

class TestEvent implements DomainEvent
{
    /**
     * @return DateTime
     */
    public function occurredOn()
    {
        return new DateTime();
    }
}

class TestListener extends AmqpExchangeListener
{
    /**
     * @var array
     */
    private $events;

    public function receivedEvents()
    {
        return $this->events;
    }

    /**
     * This method will be responsible to decide whether this listener listens to an specific
     * event type or not, given an event type name
     *
     * @param string $typeName
     *
     * @return bool
     */
    protected function listensTo($typeName)
    {
        return true;
    }

    /**
     * The action to perform
     *
     * @param stdClass $event
     *
     * @return void
     */
    protected function handle($event)
    {
        $this->receiveEvent($event);
    }

    private function receiveEvent($event)
    {
        $this->events[] = $event;
    }
}