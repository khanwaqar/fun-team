<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Contribution;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate-contributions',
    description: 'Generate monthly contributions for all employees.',
)]
class GenerateContributionsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userRepo = $this->em->getRepository(User::class);
        $contribRepo = $this->em->getRepository(Contribution::class);

        $users = $userRepo->findAll();
        $month = (new \DateTime())->format('Y-m'); // current month e.g. 2025-08
        $amount = 500; // fixed monthly contribution

        foreach ($users as $user) {
            // Check if contribution already exists for this month
            $existing = $contribRepo->findOneBy([
                'user' => $user,
                'month' => $month,
            ]);

            if ($existing) {
                $output->writeln("Contribution already exists for {$user->getEmail()} for $month.");
                continue;
            }

            $contribution = new Contribution();
            $contribution->setUser($user);
            $contribution->setAmount($amount);
            $contribution->setMonth($month);
            $contribution->setStatus('unpaid');
            $contribution->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($contribution);
            $output->writeln("Created contribution for {$user->getEmail()} for $month.");
        }

        $this->em->flush();

        $output->writeln("Monthly contributions generated successfully.");

        return Command::SUCCESS;
    }
}
