<?php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:import-employees',
    description: 'Auto-register employees from CSV or HR system.',
)]
class ImportEmployeesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Example employees list (later replace with CSV/HR API)
        $employees = [
            ['name' => 'Waqar Irshad', 'email' => 'waqar.irshad@zeropoint.hr', 'dob' => '1990-05-10'],
            ['name' => 'Ikrar Hussain', 'email' => 'ikrar@zeropoint.hr', 'dob' => '1992-11-20'],
        ];

        foreach ($employees as $emp) {
            $user = new User();
            $user->setName($emp['name']);
            $user->setEmail($emp['email']);
            $user->setDob(new \DateTime($emp['dob']));
            $user->setJoinedAt(new \DateTime());
            $user->setRoles(['ROLE_USER']);

            // Auto-generate password
            $plainPassword = "zeropoint"; // e.g. 8-char password
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $this->em->persist($user);
            $output->writeln("Created: {$emp['email']} / Password: $plainPassword");
        }

        $this->em->flush();
        return Command::SUCCESS;
    }
}
