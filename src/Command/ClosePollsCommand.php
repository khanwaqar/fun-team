<?php

namespace App\Command;

use App\Service\PollService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:close-polls',
    description: 'Close expired polls and select winners'
)]
class ClosePollsCommand extends Command
{
    public function __construct(private PollService $pollService) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->pollService->closeExpiredPolls();
        $output->writeln('Expired polls closed and winners selected.');
        return Command::SUCCESS;
    }
}
