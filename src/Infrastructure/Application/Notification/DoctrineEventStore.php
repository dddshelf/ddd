<?php

namespace Ddd\Infrastructure\Application\Notification;

use Ddd\Application\EventStore;
use Doctrine\ORM\EntityRepository;
use JMS\Serializer\SerializerBuilder;
use Ddd\Domain\Event\StoredEvent;

class DoctrineEventStore extends EntityRepository implements EventStore
{
    private $serializer;

    public function append($aDomainEvent)
    {
        $storedEvent = new StoredEvent(
            get_class($aDomainEvent),
            $aDomainEvent->occurredOn(),
            $this->serializer()->serialize($aDomainEvent, 'json')
        );

        $this->getEntityManager()->persist($storedEvent);
        $this->getEntityManager()->flush($storedEvent);
    }

    public function allStoredEventsSince($anEventId)
    {
        $query = $this->createQueryBuilder('e');
        if ($anEventId) {
            $query->where('e.eventId > :eventId');
            $query->setParameters(array('eventId' => $anEventId));
        }
        $query->orderBy('e.eventId');

        return $query->getQuery()->getResult();
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    private function serializer()
    {
        if (null === $this->serializer) {
            $this->serializer =
                SerializerBuilder::create()
                    ->addMetadataDir(__DIR__ . '/../../../Infrastructure/Application/Serialization/JMS/Config')
                    ->setCacheDir(__DIR__ . '/../../../../var/cache/jms-serializer')
                ->build()
            ;
        }

        return $this->serializer;
    }
}
