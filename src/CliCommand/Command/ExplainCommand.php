<?php

namespace Startwind\Forrest\CliCommand\Command;

use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExplainCommand extends CommandCommand
{
    protected static $defaultName = 'commands:explain';
    protected static $defaultDescription = 'Show a specific command. It will not run it.';

    protected function configure(): void
    {
        parent::configure();
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The commands identifier.');
        $this->setAliases(['explain']);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->enrichRepositories();

        $commandIdentifier = $input->getArgument('identifier');

        $command = $this->getCommand($commandIdentifier);

        OutputHelper::writeInfoBox($output, [
            'Explanation of "' . $commandIdentifier . '":'
        ]);

        $output->writeln([$command->getPrompt(), ""]);

        $explanation = OutputHelper::indentText($command->getExplanation(), 2, 80, ' |');

        $output->writeln($explanation);
        $output->writeln([
            '',
            'To run this command type: ',
            '',
            'forrest run ' . $commandIdentifier,
            '',
        ]);


        return SymfonyCommand::SUCCESS;
    }
}
