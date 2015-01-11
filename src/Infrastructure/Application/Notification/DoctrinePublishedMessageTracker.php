<?php

namespace Ddd\Infrastructure\Application\Notification;

use Ddd\Application\Notification\PublishedMessageTracker;
use Ddd\Domain\Event\StoredEvent;
use Doctrine\ORM\EntityRepository;
use Lw\Infrastructure\Application\PublishedMessage;

class DoctrinePublishedMessageTracker extends EntityRepository implements PublishedMessageTracker
{
    /**
     * @param $aTypeName
     * @return int
     */
    public function mostRecentPublishedMessageId($aTypeName)
    {
        $connection = $this->getEntityManager()->getConnection();
        $mostRecentId = $connection->fetchColumn(
            'SELECT most_recent_published_message_id FROM event_published_message_tracker WHERE type_name = ?',
            [$aTypeName]
        );

        if (!$mostRecentId) {
            return null;
        }

        return $mostRecentId;
    }

    /**
     * @param $aTypeName
     * @param StoredEvent $notification
     */
    public function trackMostRecentPublishedMessage($aTypeName, $notification)
    {
        if (!$notification) {
            return;
        }

        $maxId = $notification->eventId();

        $publishedMessage = $this->find($aTypeName);
        if (!$publishedMessage) {
            $publishedMessage = new PublishedMessage(
                $aTypeName,
                $maxId
            );
        }

        $publishedMessage->updateMostRecentPublishedMessageId($maxId);

        $this->getEntityManager()->persist($publishedMessage);
        $this->getEntityManager()->flush($publishedMessage);
    }
}
