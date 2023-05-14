<?php

namespace Startwind\Forrest\CliCommand\Repository\Command;

use Startwind\Forrest\CliCommand\Repository\RepositoryCommand;

use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class HistoryCommand extends RepositoryCommand
{
    protected static $defaultName = 'repository:command:history';
    protected static $defaultDescription = 'Creates a boilerplate for a new command repository.';

    protected function configure()
    {
        $this->setAliases(['add']);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $input->
        $resSTDIN = fopen("php://stdin", "r");
        $history = stream_get_contents($resSTDIN);
        fclose($resSTDIN);

        var_dump($history);

        //    $history = stream_get_contents(fopen("php://stdin", "r"));

        // $history = stream_get_contents(STDIN);
        $historyCommands = explode("\n", $history);


        $historyCommands = array_slice($historyCommands, count($historyCommands) - 11, 10);

        if (count($historyCommands) == 0) {
            OutputHelper::writeWarningBox($output, 'No commands founds. Usage: $ history | forrest ' . self::$defaultName);
        }

        OutputHelper::writeInfoBox($output, 'Please chose one of the following commands:');

        foreach ($historyCommands as $index => $historyCommand) {
            $commandPromptWithNumber = explode(' ', $historyCommand);
            $commandPrompt = implode(' ', array_slice($commandPromptWithNumber, 3));

            if ($index != 9) {
                $add = ' ';
            } else {
                $add = '';
            }

            $output->writeln($add . ($index + 1) . '  <fg=green>' . $commandPrompt . '</>');
        }

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $output->writeln('');

        $questionHelper->ask($input, $output, new Question('Hallo'));

        // $questionHelper->ask($input, $output, new Question('Please select a command you want to add [1-' . (count($historyCommands)) . ']: '));

        return SymfonyCommand::SUCCESS;
    }

    /**
     * Choose a repository that is editable.
     */
    private function chooseRepository(OutputInterface $output, InputInterface $input, QuestionHelper $questionHelper, int $count): int
    {
        $repoId = 0;

        while ($repoId == 0) {
            $repoId = $questionHelper->ask($input, $output, new Question('Which repository do you want to edit [1-' . $count . ']? '));
            if ((int)$repoId < 0 || $repoId > $count) {
                $output->writeln('The ID must be between 1 and ' . $count . '. Please chose again: ');
                $repoId = 0;
            }
        }

        return (int)$repoId;
    }
}
