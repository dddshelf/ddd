<?php

namespace Ddd\Infrastructure\Application\Notification;

use AMQPQueue;
use BadMethodCallException;
use Ddd\Domain\Event\StoredEvent;
use JMS\Serializer\Serializer;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use stdClass;

abstract class AmqpExchangeListener
{
    /**
     * AMQP message queue to read messages from.
     *
     * @var AMQPQueue
     */
    protected $queue;

    /**
     * Event loop.
     *
     * @var LoopInterface
     */
    protected $loop;

    /**
     * Flag to indicate if this listener is closed.
     *
     * @var bool
     */
    protected $closed = false;

    /**
     * @var TimerInterface
     */
    private $timer;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var bool
     */
    private $stop;

    public function __construct(AMQPQueue $queue, LoopInterface $loop, Serializer $serializer)
    {
        $this->queue = $queue;
        $this->loop = $loop;
        $this->timer = $this->loop->addPeriodicTimer(1, [$this, 'listen']);
        $this->serializer = $serializer;
    }

    public function listen()
    {
        if ($this->closed) {
            throw new BadMethodCallException('This listener object is closed and cannot receive any more messages.');
        }

        while ($envelope = $this->queue->get()) {
            $storedEvent = $this->serializer->deserialize(
                $envelope->getBody(),
                'Ddd\Domain\Event\StoredEvent',
                'json'
            );

            if ($this->listensTo($storedEvent->typeName())) {
                $this->handle(
                    json_decode($storedEvent->eventBody())
                );
            }

            if ($this->stop) {
                return;
            }
        }
    }

    public function stop()
    {
        $this->stop = true;
    }

    public function close()
    {
        if ($this->closed) {
            return;
        }

        $this->loop->cancelTimer($this->timer);
        unset($this->queue);
        $this->closed = true;
    }

    /**
     * This method will be responsible to decide whether this listener listens to an specific
     * event type or not, given an event type name
     *
     * @param string $typeName
     *
     * @return bool
     */
    abstract protected function listensTo($typeName);

    /**
     * The action to perform
     *
     * @param stdClass $event
     *
     * @return void
     */
    abstract protected function handle($event);
}