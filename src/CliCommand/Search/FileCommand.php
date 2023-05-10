<?php

namespace Startwind\Forrest\CliCommand\Search;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Parameters\FileParameter;
use Startwind\Forrest\Output\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class FileCommand extends SearchCommand
{
    protected static $defaultName = 'search:file';
    protected static $defaultDescription = 'Search for commands that fit the given file.';

    protected function configure(): void
    {
        $this->addArgument('filename', InputArgument::REQUIRED, 'The filename you want to get commands for.');
        $this->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Run the command without asking for permission.', false);
        $this->setAliases(['file']);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        OutputHelper::renderHeader($output);

        $this->enrichRepositories();

        $filename = $input->getArgument('filename');

        /** @var \Symfony\Component\Console\Helper\QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        if (!file_exists($filename)) {
            $this->renderErrorBox('File not found.');
            return SymfonyCommand::FAILURE;
        }

        $filenames = [$filename];
        if (is_dir($filename)) {
            $filenames[] = FileParameter::DIRECTORY;
        }

        $fileCommands = $this->search(function (Command $command, $config) {
            $parameters = $command->getParameters();
            foreach ($parameters as $parameter) {
                if ($parameter instanceof FileParameter) {
                    if ($parameter->isCompatibleWithFiles($config['filenames'])) {
                        return true;
                    }
                }
            }
            return false;
        }, ['filenames' => $filenames]);

        $this->renderInfoBox('This is a list of commands that are applicable to the given file or file type.');

        if (empty($fileCommands)) {
            $this->renderErrorBox('No commands found that match this file type.');
            return SymfonyCommand::FAILURE;
        }

        OutputHelper::renderCommands($output, $fileCommands, null, -1, count($fileCommands) > 1);

        $output->writeln('');

        if (count($fileCommands) == 1) {
            $commandIdentifier = array_key_first($fileCommands);
            $command = array_pop($fileCommands);
            if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('  Do you want to run "' . $command->getName() . '" (y/n)? '), false)) {
                return SymfonyCommand::FAILURE;
            }
        } else {
            $commandNumber = (int)$questionHelper->ask($input, $output, new Question('  Which command do you want to run [1-' . count($fileCommands) . ']? '));

            $commandIdentifier = array_keys($fileCommands)[count($fileCommands) - $commandNumber];
            $command = $fileCommands[$commandIdentifier];
        }

        $output->writeln('');

        $values = [$this->getParameterIdentifier($command, $filenames) => $filename];

        return $this->runCommand($commandIdentifier, $values);
    }

    /**
     * Return the identifier of the parameter that fits the filename.
     */
    private function getParameterIdentifier(Command $command, array $filenames): string
    {
        foreach ($command->getParameters() as $identifier => $parameter) {
            if ($parameter instanceof FileParameter) {
                if ($parameter->isCompatibleWithFiles($filenames)) {
                    return $identifier;
                }
            }
        }
        throw new \RuntimeException('No parameter found that excepts the given file name.');
    }
}
