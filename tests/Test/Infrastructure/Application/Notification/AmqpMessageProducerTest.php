<?php

namespace Ddd\Test\Infrastructure\Application\Notification;

use DateTime;
use Ddd\Infrastructure\Application\Notification\AmqpMessageProducer;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use React\EventLoop\Factory;

class AmqpMessageProducerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AmqpMessageProducer
     */
    private $messageProducer;

    /**
     * @var ObjectProphecy
     */
    private $exchange;

    /**
     * @var array
     */
    private $publishedMessages = [];

    /**
     * @test
     */
    public function itShouldBeAbleToSendMessagesToAnAmqpExchange()
    {
        $this->exchange = $this->prophesize('AMQPExchange');

        $self = $this;

        $this->exchange->publish(Argument::cetera())->will(function($args) use ($self) {
            $self->publishedMessages[] = $args[0];
        });

        $loop = Factory::create();

        $this->messageProducer = new AmqpMessageProducer(
            $this->exchange->reveal(),
            $loop
        );

        $this->messageProducer->send('exchange-test', 'This is a test1', 'test', 1, (new DateTime())->modify('+1 second'));
        $this->messageProducer->send('exchange-test', 'This is a test2', 'test', 2, (new DateTime())->modify('+5 second'));
        $this->messageProducer->send('exchange-test', 'This is a test3', 'test', 3, (new DateTime())->modify('+10 second'));

        sleep(1);

        $loop->tick();

        $this->assertCount(
            3,
            $this->publishedMessages
        );
    }
}
