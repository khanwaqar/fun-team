<?php

namespace App\Service;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

class EventService
{
    public function __construct(
        private EventRepository $eventRepo,
        private EntityManagerInterface $em
    ) {}

    public function createEvent(string $title, \DateTimeInterface $date, string $type, ?string $description = null): Event
    {
        $event = new Event();
        $event->setTitle($title)
              ->setDate($date)
              ->setType($type)
              ->setDescription($description)
              ->setStatus('planned');

        $this->em->persist($event);
        $this->em->flush();

        return $event;
    }

    public function getNextEvent(): ?Event
    {
        return $this->eventRepo->createQueryBuilder('e')
            ->where('e.date >= :today')
            ->setParameter('today', (new \DateTime())->format('Y-m-d'))
            ->orderBy('e.date', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getUpcomingEvents(int $limit = 5): array
    {
        return $this->eventRepo->createQueryBuilder('e')
            ->where('e.date >= :today')
            ->setParameter('today', (new \DateTime())->format('Y-m-d'))
            ->orderBy('e.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
