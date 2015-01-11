<?php

namespace Ddd\Infrastructure\Application\Notification;

use PhpAmqpLib\Connection\AMQPConnection;

abstract class RabbitMqMessaging
{
    protected $connections;
    protected $channels;

    public function __construct(AMQPConnection $aConnection)
    {
        $this->channels = [];
        $this->connections = [];
    }

    private function connect($exchangeName)
    {
        $connectionKey = $exchangeName;
        if (isset($this->connections[$connectionKey])) {
            return;
        }

        $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->exchange_declare($exchangeName, 'fanout', false, true, false);
        $channel->queue_declare($exchangeName, false, true, false, false);
        $channel->queue_bind($exchangeName, $exchangeName);

        $this->connections[$connectionKey] = $connection;
        $this->channels[$connectionKey] = $channel;
    }

    public function open($exchangeName)
    {

    }

    protected function channel($exchangeName)
    {
        $this->connect($exchangeName);

        return $this->channels[$exchangeName];
    }

    public function close($exchangeName)
    {
        $this->channels[$exchangeName]->close();
        $this->connections[$exchangeName]->close();
    }
}
