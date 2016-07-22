<?php

namespace Ddd\Application\Notification;

interface MessageProducer
{
    public function open($exchangeName);

    /**
     * @param $exchangeName
     * @param string $notificationMessage
     * @param string $notificationType
     * @param int $notificationId
     * @param \DateTimeInterface $notificationOccurredOn
     * @return
     */
    public function send($exchangeName, $notificationMessage, $notificationType, $notificationId, \DateTimeInterface $notificationOccurredOn);

    public function close($exchangeName);
}
