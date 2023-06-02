<?php

namespace Startwind\Forrest\CliCommand\Ai;

use Startwind\Forrest\CliCommand\Command\CommandCommand;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Startwind\Forrest\Command\Answer\Answer;

class AskCommand extends CommandCommand
{
    protected static $defaultName = 'ai:ask';
    protected static $defaultDescription = 'Suggest an answer to a question.';

    protected function configure(): void
    {
        $this->addArgument('question', InputArgument::IS_ARRAY, 'The question you want to have answered.');
        $this->setAliases(['ai']);
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Run the command without asking for permission.');

        parent::configure();
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $this->enrichRepositories();

        \Startwind\Forrest\Output\OutputHelper::renderHeader($output);

        $aiQuestion = implode(' ', $input->getArgument('question'));

        OutputHelper::writeInfoBox($output, [
            ucfirst($aiQuestion)
        ]);

        $answers = $this->getRepositoryCollection()->ask($aiQuestion);

        foreach ($answers as $repositoryName => $repoAnswers) {
            foreach ($repoAnswers as $answer) {
                /** @var Answer $answer */
                $output->writeln(OutputHelper::indentText($this->formatCliText($answer->getAnswer())));
                return $this->runCommand($answer->getCommand(), []);
            }
        }

        return SymfonyCommand::SUCCESS;
    }

    private function formatCliText(string $text): string
    {
        preg_match_all('#```shell((.|\n)*?)```#', $text, $matches);

        if (count($matches[1]) == 1) {
            $shell = $matches[1][0];

            $shellNew = implode("\n", OutputHelper::indentText(trim($shell), 2, 100, ' | '));

            $text = str_replace($matches[0][0], $shellNew, $text);
        }

        preg_match_all('#`(.*)`#', $text, $matches);

        if (count($matches[1]) > 0) {

            foreach ($matches[0] as $key => $match) {
                $text = str_replace($match, '<options=bold>' . $matches[1][$key] . '</>', $text);
            }
        }

        return $text;
    }
}
