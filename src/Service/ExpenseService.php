<?php

namespace App\Service;

use App\Repository\ExpenseRepository;

class ExpenseService
{
    public function __construct(private ExpenseRepository $expenseRepo) {}

    public function getTotalExpenses(): int
    {
        return $this->expenseRepo->getTotalExpenses();
    }

    public function getRecentExpenses(int $limit = 5): array
    {
        return $this->expenseRepo->findRecentExpenses($limit);
    }
}
