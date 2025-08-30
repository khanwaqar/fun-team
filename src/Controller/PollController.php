<?php

namespace App\Controller;

use App\Entity\PollOption;
use App\Entity\PollVote;
use App\Service\PollService;
use Doctrine\ORM\EntityManagerInterface; // ✅ add this
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employee/poll')]
#[IsGranted('ROLE_USER')]
class PollController extends AbstractController
{
    public function __construct(private PollService $pollService) {}

    #[Route('/vote/{id}', name: 'employee_poll_vote', methods: ['POST'])]
    public function vote(PollOption $option, EntityManagerInterface $em, Request $request): RedirectResponse
    {
        $user = $this->getUser();

        // Prevent double voting
        if ($option->getPoll()->hasUserVoted($user)) {
            $this->addFlash('warning', 'You already voted for this poll.');
            return $this->redirectToRoute('dashboard');
        }

        $vote = new PollVote();
        $vote->setUser($user);
        $vote->setOption($option);
        $vote->setPoll($option->getPoll());
        $vote->setCreatedAt(new \DateTimeImmutable());

        $em->persist($vote);
        $em->flush();

        $this->addFlash('success', 'Your vote has been recorded.');
        return $this->redirectToRoute('dashboard');
    }
}
