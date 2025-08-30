<?php

namespace App\Controller\Admin;

use App\Entity\Contribution;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/contribution')]
class ContributionController extends BaseAdminController
{
    #[Route('/', name: 'admin_contribution_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $contributions = $em->getRepository(Contribution::class)->findAll();
        return $this->render('admin/contribution/index.html.twig', compact('contributions'));
    }

    #[Route('/mark-paid/{id}', name: 'admin_contribution_mark_paid')]
    public function markPaid(int $id, EntityManagerInterface $em): Response
    {
        $contribution = $em->getRepository(Contribution::class)->find($id);
        if ($contribution) {
            $contribution->setStatus('paid');
            $em->flush();
            $this->addFlash('success', 'Contribution marked as paid.');
        } else {
            $this->addFlash('danger', 'Contribution not found.');
        }

        return $this->redirectToRoute('admin_contribution_index');
    }
}
