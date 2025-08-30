<?php

namespace App\Controller;

use App\Service\FundsService;
use App\Service\EventService;
use App\Service\PollService;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    public function __construct(
        private FundsService $fundsService,
        private EventService $eventService,
        private PollService $pollService,
        private UserRepository $userRepo
    ) {}

    #[Route('/', name: 'dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();

        // Contributions summary
        $paid = $this->fundsService->getTotalPaidByUser($user);
        $unpaid = $this->fundsService->getTotalUnpaidByUser($user);
        $balance = $this->fundsService->getBalance($user);

        // Upcoming birthdays (next 7 days)
        $today = new \DateTime();
        $inSevenDays = (clone $today)->modify('+7 days');

        $users = $this->userRepo->findAll();
        $upcomingBirthdays = array_filter($users, function($u) use ($today, $inSevenDays) {
            $dob = $u->getDob();
            if (!$dob) return false;
            $dobThisYear = (clone $dob)->setDate((int)$today->format('Y'), (int)$dob->format('m'), (int)$dob->format('d'));
            return $dobThisYear >= $today && $dobThisYear <= $inSevenDays;
        });

        // Events
        $nextEvent = $this->eventService->getNextEvent();
        $upcomingEvents = $this->eventService->getUpcomingEvents(5);

        // Polls
        $activePolls = $this->pollService->getActivePolls();
        $pollsWithOptions = [];
        foreach ($activePolls as $poll) {
            $hasVoted = $poll->hasUserVoted($user);
            $options = $poll->getPollOptions(); // PollOption entities
            $votes = [];
            if ($hasVoted) {
                foreach ($options as $opt) {
                    $votes[] = [
                        'title' => $opt->getTitle(),
                        'count' => $opt->getVotesCount()
                    ];
                }
            }
            $pollsWithOptions[] = [
                'poll' => $poll,
                'hasVoted' => $hasVoted,
                'options' => $options,
                'votes' => $votes
            ];
        }

        $completedPolls = $this->pollService->getCompletedPolls();

        return $this->render('dashboard/index.html.twig', [
            'paid' => $paid,
            'unpaid' => $unpaid,
            'balance' => $balance,
            'upcomingBirthdays' => $upcomingBirthdays,
            'nextEvent' => $nextEvent,
            'upcomingEvents' => $upcomingEvents,
            'activePolls' => $activePolls,
            'completedPolls' => $completedPolls,
            'pollsWithOptions' => $pollsWithOptions,
        ]);
    }
}
