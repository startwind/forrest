<?php

namespace Startwind\Forrest\CliCommand\Ai;

use Startwind\Forrest\CliCommand\Command\CommandCommand;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class SuggestCommand extends CommandCommand
{
    protected static $defaultName = 'ai:suggest';
    protected static $defaultDescription = 'Suggest an answer to a question.';

    protected function configure(): void
    {
        $this->addArgument('question', InputArgument::IS_ARRAY, 'The question you want to have answered.');
        $this->setAliases(['ai']);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $aiQuestion = implode(' ', $input->getArgument('question'));

        OutputHelper::writeInfoBox($output, [
            'Forrest AI is trying to find an answer to your question:',
            '',
            ucfirst($aiQuestion)
        ]);

        sleep(1);
        $output->writeln('');

        if (str_contains($aiQuestion, 'delete')) {
            $output->writeln('For deleting a file in Linux type:');
            $output->writeln('');
            OutputHelper::writeWarningBox($output, 'rm LICENSE');
            $output->writeln('');
        } elseif (str_contains($aiQuestion, 'unpack') || str_contains($aiQuestion, 'decompress')) {
            $output->writeln('For unpacking a tar file in Linux type:');
            $output->writeln('');
            OutputHelper::writeWarningBox($output, 'tar -zxvf ${filename}');
            $output->writeln('');
        }

        /** @var \Symfony\Component\Console\Helper\QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $questionHelper->ask($input, $output, new Question('Do you want to run this command? (y/n): ', false));

        return SymfonyCommand::SUCCESS;
    }
}
