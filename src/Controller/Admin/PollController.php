<?php

namespace App\Controller\Admin;

use App\Entity\Poll;
use App\Entity\PollOption;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/poll')]
class PollController extends BaseAdminController
{
    #[Route('/', name: 'admin_poll_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $polls = $em->getRepository(Poll::class)->findAll();
        return $this->render('admin/poll/index.html.twig', compact('polls'));
    }

    #[Route('/create', name: 'admin_poll_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {

            $poll = new Poll();
            $poll->setTitle($request->request->get('title'));
            $poll->setDescription($request->request->get('description'));
            $poll->setStatus('active');
            $poll->setCreatedAt(new \DateTimeImmutable());

            $eventId = $request->request->get('event_id');
            if ($eventId) {
                $event = $em->getRepository(Event::class)->find($eventId);
                $poll->setEvent($event);
            }

            $endDate = $request->request->get('endDate');
            if ($endDate) {
                $poll->setEndDate(new \DateTime($endDate));
            }

            $em->persist($poll);

            // Create options
            $optionsInput = $request->request->get('options'); // this is an array
            $optTitles = explode(',', $optionsInput);
            foreach ($optTitles as $option) {
                if ($option === '') continue;
                $opt = new PollOption();
                $opt->setTitle($option);
                $opt->setPoll($poll);
                $em->persist($opt);
            }

            $em->flush();

            $this->addFlash('success', 'Poll created successfully.');
            return $this->redirectToRoute('admin_poll_index');
        }

        $events = $em->getRepository(Event::class)->findAll();
        return $this->render('admin/poll/create.html.twig', compact('events'));
    }

    #[Route('/{id}', name: 'admin_poll_show', requirements: ['id' => '\d+'])]
    public function show(int $id, EntityManagerInterface $em): Response
    {
        $poll = $em->getRepository(Poll::class)->find($id);

        if (!$poll) {
            throw $this->createNotFoundException('Poll not found.');
        }

        // Fetch related options
        $options = $em->getRepository(PollOption::class)->findBy(['poll' => $poll]);

        return $this->render('admin/poll/show.html.twig', [
            'poll' => $poll,
            'options' => $options
        ]);
    }
}
