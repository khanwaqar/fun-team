<?php

namespace App\Service;

use App\Entity\Poll;
use App\Entity\PollOption;
use App\Entity\PollVote;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PollRepository;
use App\Repository\PollVoteRepository;

class PollService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PollRepository $pollRepo,
        private PollVoteRepository $pollVoteRepo
    ) {}

    /**
     * Get all active polls
     */
    public function getActivePolls(): array
    {
        return $this->pollRepo->findBy(['status' => 'active']);
    }

    /**
     * Check if user has voted for a poll
     */
    public function hasUserVoted(Poll $poll, User $user): bool
    {
        return (bool) $this->pollVoteRepo->findOneBy([
            'poll' => $poll,
            'user' => $user,
        ]);
    }

    /**
     * Cast a vote for a user
     */
    public function vote(PollOption $option, User $user): bool
    {
        $poll = $option->getPoll();

        // Check if user already voted
        if ($this->hasUserVoted($poll, $user)) {
            return false;
        }

        // Increment option votes
        $option->incrementVotes();

        // Create PollVote record
        $vote = new PollVote();
        $vote->setUser($user)
             ->setPoll($poll)
             ->setOption($option);

        $this->em->persist($option);
        $this->em->persist($vote);
        $this->em->flush();

        return true;
    }

    /**
     * Get active polls linked to upcoming events (next 30 days)
     */
    public function getActivePollsForUpcomingEvents(int $days = 30): array
    {
        $today = new \DateTime();
        $futureDate = (new \DateTime())->modify("+$days days");

        $qb = $this->pollRepo->createQueryBuilder('p')
            ->leftJoin('p.event', 'e')
            ->where('p.status = :active')
            ->setParameter('active', 'active');

        // Only polls linked to events within next $days
        $qb->andWhere('e.date BETWEEN :today AND :futureDate')
        ->setParameter('today', $today->format('Y-m-d'))
        ->setParameter('futureDate', $futureDate->format('Y-m-d'))
        ->orderBy('e.date', 'ASC');

        return $qb->getQuery()->getResult();
    }
    // Fetch completed polls (winner selected)

    public function getCompletedPolls(): array
    {
        return $this->pollRepo->createQueryBuilder('p')
            ->leftJoin('p.winnerOption', 'w')
            ->addSelect('w')
            ->where('p.status = :completed')
            ->setParameter('completed', 'completed')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Close polls that have reached endDate or event date and select the winner
     */
    public function closeExpiredPolls(): void
    {
        $today = new \DateTime();

        // Fetch active polls with endDate <= today or linked event date <= today
        $qb = $this->pollRepo->createQueryBuilder('p')
            ->leftJoin('p.event', 'e')
            ->where('p.status = :active')
            ->setParameter('active', 'active')
            ->andWhere('p.endDate <= :today OR e.date <= :today')
            ->setParameter('today', $today->format('Y-m-d'));

        $expiredPolls = $qb->getQuery()->getResult();

        foreach ($expiredPolls as $poll) {
            $options = $poll->getOptions();
            if (count($options) === 0) {
                $poll->setStatus('closed');
                continue;
            }

            // Determine winner (highest votes)
            usort($options, fn($a, $b) => $b->getVotes() <=> $a->getVotes());
            $winner = $options[0] ?? null;

            if ($winner) {
                // Optional: store winner in poll (add winnerOption field)
                $poll->setStatus('completed');
                // You can also add $poll->setWinnerOption($winner) if you create a field
            } else {
                $poll->setStatus('closed');
            }

            $this->em->persist($poll);
        }

        $this->em->flush();
    }


}
