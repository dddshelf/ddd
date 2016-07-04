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

class AmqpMessageProducer implements MessageProducer
{
    /**
     * AMQP message exchange to send messages to.
     *
     * @var AMQPExchange
     */
    protected $exchange;

    public function __construct(AMQPExchange $exchange)
    {
        $this->exchange = $exchange;
    }

    public function close($exchangeName)
    {

    }

    public function open($exchangeName)
    {

    }

    public function send($exchangeName, $notificationMessage, $notificationType, $notificationId, \DateTimeInterface $notificationOccurredOn)
    {
        $this->exchange->publish(
            $notificationMessage,
            null,
            AMQP_NOPARAM,
            [
                'type'          => $notificationType,
                'timestamp'     => $notificationOccurredOn->getTimestamp(),
                'message_id'    => $notificationId
            ]
        );
    }
}