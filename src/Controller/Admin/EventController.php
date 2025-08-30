<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/event')]
class EventController extends BaseAdminController
{
    #[Route('/', name: 'admin_event_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $events = $em->getRepository(Event::class)->findAll();
        return $this->render('admin/event/index.html.twig', compact('events'));
    }

    #[Route('/create', name: 'admin_event_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $event = new Event();
            $event->setTitle($request->request->get('title'));
            $event->setDescription($request->request->get('description'));
            $event->setLocation($request->request->get('location'));
            $event->setType($request->request->get('event_type'));
            $event->setDate(new \DateTime($request->request->get('date')));
            $event->setCreatedAt(new \DateTimeImmutable());
            $event->setStatus('upcoming');

            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Event created successfully.');
            return $this->redirectToRoute('admin_event_index');
        }

        return $this->render('admin/event/create.html.twig');
    }

    #[Route('/{id}', name: 'admin_event_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('admin/event/show.html.twig', compact('event'));
    }

    #[Route('/{id}/edit', name: 'admin_event_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $event->setTitle($request->request->get('title'));
            $event->setDescription($request->request->get('description'));
            $event->setLocation($request->request->get('location'));
            $event->setType($request->request->get('event_type'));
            $event->setDate(new \DateTime($request->request->get('date')));
            $event->setStatus($request->request->get('status'));

            $em->flush();

            $this->addFlash('success', 'Event updated successfully.');
            return $this->redirectToRoute('admin_event_index');
        }

        return $this->render('admin/event/edit.html.twig', compact('event'));
    }

    #[Route('/{id}/delete', name: 'admin_event_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Event $event, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $em->remove($event);
            $em->flush();

            $this->addFlash('success', 'Event deleted successfully.');
        }

        return $this->redirectToRoute('admin_event_index');
    }
}
