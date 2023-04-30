<?php

namespace Startwind\Forrest\CliCommand\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends CommandCommand
{
    protected static $defaultName = 'commands:show';
    protected static $defaultDescription = 'List all command that are registered in the repositories.';

    private array $warning = [
        "Be careful. Please only run command that you understand. We only have limited control",
        "of repositories that are not owned by this project."
    ];

    protected function configure()
    {
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The commands identifier.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initRepositories();

        $command = $this->getCommand($input->getArgument('identifier'));

        $commands = explode("\n", $command->getCommand());

        $this->writeWarning($output, $this->warning);

        $output->writeln('');
        $output->writeln('  Command to be run:');
        $this->writeInfo($output, $commands);
        $output->writeln('');

        return SymfonyCommand::SUCCESS;
    }
}
