<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\ContributionRepository;
use App\Repository\EventRepository;
use App\Repository\PollRepository;
use App\Repository\ExpenseRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\BaseAdminController;
use App\Service\ExpenseService;

#[Route('/admin')]
class DashboardController extends BaseAdminController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(
        UserRepository $userRepo,
        ContributionRepository $contributionRepo,
        EventRepository $eventRepo,
        PollRepository $pollRepo,
        ExpenseRepository $expenseRepo,
        ExpenseService $expenseService
    ): Response
    {
        // Counts
        $userCount = $userRepo->count([]);
        $contributionCount = $contributionRepo->count([]);
        $eventCount = $eventRepo->count([]);
        $activePollCount = $pollRepo->count(['status' => 'active']);

        // Recent activity
        $recentUsers = $userRepo->findBy([], ['joinedAt' => 'DESC'], 5);
        $recentEvents = $eventRepo->findBy([], ['date' => 'DESC'], 5);
        $recentPolls = $pollRepo->findBy([], ['createdAt' => 'DESC'], 5);
        $recentContributions = $contributionRepo->findBy([], ['month' => 'DESC'], 5);

        // Financial summaries
        $totalExpenses = $expenseService->getTotalExpenses();
        $paidTotal = $contributionRepo->getTotalByStatus('paid');
        $unpaidTotal = $contributionRepo->getTotalByStatus('unpaid');
        $balanceTotal = $paidTotal - $totalExpenses;

        
        $recentExpenses = $expenseService->getRecentExpenses(5);

        return $this->render('admin/dashboard/index.html.twig', [
            'userCount' => $userCount,
            'contributionCount' => $contributionCount,
            'eventCount' => $eventCount,
            'activePollCount' => $activePollCount,
            'recentUsers' => $recentUsers,
            'recentEvents' => $recentEvents,
            'recentPolls' => $recentPolls,
            'recentContributions' => $recentContributions,
            'paidTotal' => $paidTotal,
            'unpaidTotal' => $unpaidTotal,
            'expensesTotal' => $totalExpenses,
            'balanceTotal' => $balanceTotal,
            'recentExpenses' => $recentExpenses,
        ]);
    }
}
