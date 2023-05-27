<?php

namespace Startwind\Forrest\CliCommand\Search;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Parameters\FileParameter;
use Startwind\Forrest\Output\OutputHelper;
use Startwind\Forrest\Repository\FileRepository;
use Startwind\Forrest\Repository\SearchAware;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FileCommand extends SearchCommand
{
    public const COMMAND_NAME = 'search:file';

    protected static $defaultName = self::COMMAND_NAME;
    protected static $defaultDescription = 'Search for commands that fit the given file.';

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('filename', InputArgument::REQUIRED, 'The filename you want to get commands for.');
        $this->addArgument('pattern', InputArgument::OPTIONAL, 'Filter the results for a given pattern.');

        $this->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Run the command without asking for permission.', false);

        $this->setAliases(['file']);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        OutputHelper::renderHeader($output);

        $this->enrichRepositories();

        $filename = $input->getArgument('filename');
        $pattern = $input->getArgument('pattern');

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

        $fileCommands = $this->getRepositoryCollection()->searchByFile($filenames);

        if ($pattern) {
            foreach ($fileCommands as $key => $fileCommand) {
                if (str_contains(strtolower($fileCommand->getName()), strtolower($pattern))) {
                    continue;
                }
                if (str_contains(strtolower($fileCommand->getDescription()), strtolower($pattern))) {
                    continue;
                }
                unset($fileCommands[$key]);
            }
        }

        $this->renderInfoBox('This is a list of commands that are applicable to the given file or file type.');

        if (empty($fileCommands)) {
            $this->renderErrorBox('No commands found that match this file type.');
            return SymfonyCommand::FAILURE;
        }

        $command = OutputHelper::renderCommands(
            $output,
            $input,
            $questionHelper,
            $fileCommands,
            null,
            -1,
            true
        );

        if ($command === false) {
            return SymfonyCommand::FAILURE;
        }

        $output->writeln('');

        $values = [$this->getParameterIdentifier($command, $filenames) => $filename];

        return $this->runCommand($command, $values);
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
