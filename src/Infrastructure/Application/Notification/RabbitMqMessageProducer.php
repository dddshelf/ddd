<?php

namespace Ddd\Infrastructure\Application\Notification;

use Ddd\Application\Notification\MessageProducer;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqMessageProducer extends RabbitMqMessaging implements MessageProducer
{
    /**
     * @param $exchangeName
     * @param string $notificationMessage
     * @param string $notificationType
     * @param int $notificationId
     * @param \DateTime $notificationOccurredOn
     */
    public function send($exchangeName, $notificationMessage, $notificationType, $notificationId, \DateTime $notificationOccurredOn)
    {
        $this->channel($exchangeName)->basic_publish(
            new AMQPMessage(
                $notificationMessage,
                ['type' => $notificationType, 'timestamp' => $notificationOccurredOn->getTimestamp(), 'message_id' => $notificationId]
            ),
            $exchangeName
        );
    }
}
