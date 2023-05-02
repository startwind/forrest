<?php

namespace Startwind\Forrest\CliCommand\Repository;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

class CreateCommand extends RepositoryCommand
{
    protected static $defaultName = 'repository:create';
    protected static $defaultDescription = 'Creates a boilerplate for a new command repository.';

    protected function configure()
    {
        $this->addArgument('repositoryFileName', InputArgument::REQUIRED, 'The filename of new repository.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $content = [];

        $repositoryFileName = $input->getArgument('repositoryFileName');

        $output->writeln('');

        $questionHelper = $this->getHelper('question');

        if (file_exists($repositoryFileName)) {
            $overwrite = $questionHelper->ask($input, $output, new ConfirmationQuestion('File already exists. Do you want to overwrite it? [y/n] ', false));
            if (!$overwrite) {
                $this->writeWarning($output, 'No repository created. File already exists.');
                return SymfonyCommand::FAILURE;
            }
        }

        $name = $questionHelper->ask($input, $output, new Question('Name of the repository [default: "local commands"]: ', 'local commands'));
        $identifierSuggestion = $this->getIdentifierSuggestion($name);
        $description = $questionHelper->ask($input, $output, new Question('Description of the repository [default: "Commands for local usage"]: ', 'Commands for local usage'));
        $identifier = $questionHelper->ask($input, $output, new Question('Identifier of the repository [default: "' . $identifierSuggestion . '"]: ', $identifierSuggestion));

        $content['repository'] = [
            'name' => $name,
            'description' => $description,
            'identifier' => $identifier
        ];

        $content['commands'] = [
            'my-unique-command-name' => [
                'name' => 'foo:bar',
                'description' => 'Command description',
                'prompt' => 'ls -lah'
            ]
        ];

        file_put_contents($repositoryFileName, Yaml::dump($content, 4));

        $this->writeInfo($output, 'Repository file "' . $repositoryFileName . '" successfully created.');

        $register = $questionHelper->ask($input, $output, new ConfirmationQuestion('Do you already want to register the repository? [y/n] ', false));

        if ($register) {
            $this->registerRepository($identifier, $name, $description, $repositoryFileName);
            $this->writeInfo($output, 'Repository file "' . $repositoryFileName . '" successfully registered.');
        }

        return SymfonyCommand::SUCCESS;
    }
}
