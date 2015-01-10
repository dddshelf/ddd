<?php

namespace Ddd\Application\Notification;

interface MessageProducer
{
    /**
     * @param $exchangeName
     * @param string $notificationMessage
     * @param string $notificationType
     * @param int $notificationId
     * @param \DateTime $notificationOccurredOn
     * @return
     */
    public function send($exchangeName, $notificationMessage, $notificationType, $notificationId, \DateTime $notificationOccurredOn);
    public function close($exchangeName);
}
