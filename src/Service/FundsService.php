<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\ContributionRepository;
use App\Repository\ExpenseRepository;

class FundsService
{
    public function __construct(
        private ContributionRepository $contribRepo,
        private ExpenseRepository $expenseRepo
    ) {}

    public function getUserContributions(User $user): array
    {
        $contributions = $this->contribRepo->findBy(['user' => $user]);

        $paid = 0;
        $unpaid = 0;
        $total = 0;

        foreach ($contributions as $c) {
            $total += $c->getAmount();
            if ($c->getStatus() === 'paid') {
                $paid += $c->getAmount();
            } else {
                $unpaid += $c->getAmount();
            }
        }

        return [
            'total' => $total,
            'paid' => $paid,
            'unpaid' => $unpaid,
        ];
    }

    public function getTotalExpenses(): int
    {
        $expenses = $this->expenseRepo->findAll();
        return array_sum(array_map(fn($e) => $e->getAmount(), $expenses));
    }

    public function getBalance(User $user): int
    {
        $userContrib = $this->getUserContributions($user)['total'];
        $totalExpenses = $this->getTotalExpenses();
        return $userContrib - $totalExpenses;
    }

        // Total paid contributions for a user
    public function getTotalPaidByUser(User $user): float
    {
        $result = $this->contribRepo->createQueryBuilder('c')
            ->select('SUM(c.amount)')
            ->where('c.user = :user')
            ->andWhere('c.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'paid')
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }

    // Total unpaid contributions for a user
    public function getTotalUnpaidByUser(User $user): float
    {
        $result = $this->contribRepo->createQueryBuilder('c')
            ->select('SUM(c.amount)')
            ->where('c.user = :user')
            ->andWhere('c.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'unpaid')
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }    

}
