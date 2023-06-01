<?php

namespace Startwind\Forrest\CliCommand\Search;

use Startwind\Forrest\Output\OutputHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ToolCommand extends SearchCommand
{
    protected static $defaultName = 'search:tool';
    protected static $defaultDescription = 'Search for commands that fit the given tool.';

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('tool', InputArgument::REQUIRED, 'The tool name you want to search for.');
        $this->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Run the command without asking for permission.', false);

        $this->setAliases(['tool']);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        OutputHelper::renderHeader($output);

        $this->enrichRepositories();

        $tool = $input->getArgument('tool');

        $this->renderInfoBox('This is a list of commands that match the given tool.');

        $commands = $this->getRepositoryCollection()->searchByTools([$tool]);

        $toolInformation = $this->getRepositoryCollection()->getToolInformation($tool);

        if (count($toolInformation) > 0) {
            $output->writeln(['  Information about "<options=bold>' . $tool . '</>":', '']);

            foreach ($toolInformation as $repo => $information) {
                $output->writeln($this->indentText($information->getDescription(), 0, 100, '  | '));
                if ($see = $information->getSee()) {
                    $output->writeln(['', '  For more information visit: <href=' . $see . '>' . $see . '</>', '']);
                }
            }


            $output->writeln('');
        }

        if (empty($commands)) {
            $this->renderErrorBox('No commands found that match the given tool.');
            return SymfonyCommand::FAILURE;
        }

        return $this->runFromCommands($commands);
    }

    private function indentText(string $text, int $indent = 2, int $width = 100, $prefix = ''): array
    {
        $wrapped = explode("\n", wordwrap($text, $width));

        $result = [];

        foreach ($wrapped as $line) {
            $result[] = $prefix . str_repeat(' ', $indent) . $line;
        }

        return $result;
    }

}
