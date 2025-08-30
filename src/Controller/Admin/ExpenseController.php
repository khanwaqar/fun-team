<?php

namespace App\Controller\Admin;

use App\Entity\Expense;
use App\Service\ExpenseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/expense')]
class ExpenseController extends BaseAdminController
{
    #[Route('/', name: 'admin_expense_index')]
    public function index(ExpenseService $expenseService): Response
    {
        $expenses = $expenseService->getRecentExpenses();
        $total = $expenseService->getTotalExpenses();

        return $this->render('admin/expense/index.html.twig', [
            'expenses' => $expenses,
            'totalExpenses' => $total,
        ]);
    }

    #[Route('/create', name: 'admin_expense_create', methods: ['GET','POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $amount = (int) $request->request->get('amount');
            $category = $request->request->get('category');
            $date = new \DateTime($request->request->get('date'));

            $expense = new Expense();
            $expense->setTitle($title)
                    ->setAmount($amount)
                    ->setCategory($category)
                    ->setDate($date)
                    ->setCreated(new \DateTimeImmutable());

            $em->persist($expense);
            $em->flush();

            $this->addFlash('success', 'Expense added successfully.');
            return $this->redirectToRoute('admin_expense_index');
        }

        return $this->render('admin/expense/create.html.twig');
    }
}
