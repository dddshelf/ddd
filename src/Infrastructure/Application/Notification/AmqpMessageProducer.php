<?php

namespace Ddd\Infrastructure\Application\Notification;

use AMQPExchange;
use AMQPExchangeException;
use BadMethodCallException;
use Countable;
use DateTime;
use Ddd\Application\Notification\MessageProducer;
use IteratorAggregate;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;

class AmqpMessageProducer implements Countable, IteratorAggregate, MessageProducer
{
    /**
     * AMQP message exchange to send messages to.
     *
     * @var AMQPExchange
     */
    protected $exchange;

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
     * Collection of messages waiting to be sent.
     *
     * @var array
     */
    protected $messages = array();

    /**
     * @var TimerInterface
     */
    protected $timer;

    public function __construct(AMQPExchange $exchange, LoopInterface $loop)
    {
        $this->exchange = $exchange;
        $this->loop = $loop;
        $this->timer = $this->loop->addPeriodicTimer(1, [$this, 'publish']);
    }

    /**
     * Returns the number of messages waiting to be sent. Implements the
     * countable interface.
     *
     * @return int
     */
    public function count()
    {
        return count($this->messages);
    }

    /**
     * Returns the array of messages stored. Completes the implementation of
     * the iteratorAggregate interface.
     *
     * @return array
     */
    public function getIterator()
    {
        return $this->messages;
    }

    public function publish()
    {
        if ($this->closed) {
            throw new BadMethodCallException('This producer object is closed and cannot send any more messages.');
        }

        $keysToRemove = [];

        foreach ($this->messages as $index => $message) {
            $this->exchange->publish($message['message'], null, AMQP_NOPARAM, $message['attributes']);
            $keysToRemove[] = $index;
        }

        array_map(function($index) {
            unset($this->messages[$index]);
        }, $keysToRemove);
    }

    public function close($exchangeName)
    {
        if ($this->closed) {
            return;
        }

        $this->loop->cancelTimer($this->timer);
        unset($this->exchange);
        $this->closed = true;
    }

    public function open($exchangeName)
    {
        throw new BadMethodCallException();
    }

    public function send($exchangeName, $notificationMessage, $notificationType, $notificationId, DateTime $notificationOccurredOn)
    {
        if ($this->closed) {
            throw new BadMethodCallException('This Producer object is closed and cannot send any more messages.');
        }

        $this->messages[] = [
            'message' => $notificationMessage,
            'attributes' => [
                'type' => $notificationType,
                'timestamp' => $notificationOccurredOn->getTimestamp(),
                'message_id' => $notificationId
            ]
        ];
    }
}