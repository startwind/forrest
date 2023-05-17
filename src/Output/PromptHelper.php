<?php

namespace Startwind\Forrest\Output;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\Parameters\Parameter;
use Startwind\Forrest\Command\Parameters\ParameterValue;
use Startwind\Forrest\Command\Parameters\PasswordParameter;
use Startwind\Forrest\Command\Prompt;
use Startwind\Forrest\Config\RecentParameterMemory;
use Startwind\Forrest\Runner\CommandRunner;
use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class PromptHelper
{
    protected array $runWarning = [
        "Be careful. Please only run command that you understand. We only have limited control",
        "of repositories that are not owned by this project.",
    ];

    private InputInterface $input;
    private OutputInterface $output;

    private QuestionHelper $questionHelper;

    private RecentParameterMemory $memory;

    public function __construct(
        InputInterface        $input,
        OutputInterface       $output,
        QuestionHelper        $questionHelper,
        RecentParameterMemory $memory
    )
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
        $this->memory = $memory;
    }

    public function askForPrompt(string $repositoryIdentifier, Command $command, array $predefinedParameters = []): Prompt
    {
        if ($command->isParameterMissing($predefinedParameters)) {
            $this->showCommandInformation($this->output, $command);
        }

        $parameterValues = $this->askForParameterValues($repositoryIdentifier, $command, $predefinedParameters);

        return new Prompt($command->getPrompt(), $parameterValues);
    }

    public function showFinalPrompt(Prompt $prompt): void
    {
        $this->output->writeln('');
        $this->output->writeln('  Final prompt: ');
        OutputHelper::writeInfoBox($this->output, CommandRunner::stringToMultilinePrompt($prompt->getSecurePrompt()));
    }

    private function askForParameterValues(string $repositoryIdentifier, Command $command, array $predefinedParameters = []): array
    {
        $values = [];

        $commandIdentifier = $repositoryIdentifier . ':' . $command->getName();

        foreach ($command->getParameters() as $identifier => $parameter) {

            if (array_key_exists($identifier, $predefinedParameters)) {
                $values[] = new ParameterValue($identifier, $predefinedParameters[$identifier], $parameter->getType());
                continue;
            }

            $fullParameterIdentifier = $commandIdentifier . ':' . $identifier;

            $additional = $this->getAdditionalInfo($commandIdentifier, $parameter);

            if ($parameter->getName()) {
                $name = $identifier . ' (' . $parameter->getName() . ')';
            } else {
                $name = $identifier;
            }

            if ($parameter->hasValues()) {
                $value = $this->askForEnum('  Select value for ' . $name . $additional['string'] . ': ', $parameter->getValues());
            } else {
                $question = new Question('  Select value for ' . $name . $additional['string'] . ': ', $additional['value']);
                if ($parameter instanceof PasswordParameter) {
                    $question->setHidden(true);
                    $question->setHiddenFallback(false);
                }
                $value = $this->questionHelper->ask($this->input, $this->output, $question);
            }

            $values[] = new ParameterValue($identifier, $value, $parameter->getType());

            if ($value && !($parameter instanceof PasswordParameter)) {
                $this->memory->addParameter($fullParameterIdentifier, $value);
            }
        }

        return $values;
    }

    private function askForEnum(string $question, array $values): string
    {
        if (array_is_list($values)) {
            $value = $this->questionHelper->ask($this->input, $this->output, new ChoiceQuestion($question, $values));
        } else {
            $key = $this->questionHelper->ask($this->input, $this->output, new ChoiceQuestion($question, array_keys($values)));
            $value = $values[$key];
        }

        return $value;
    }

    /**
     * Handle default and recent values for the current parameter.
     */
    private function getAdditionalInfo(string $fullParameterIdentifier, Parameter $parameter): array
    {
        if ($this->memory->hasParameter($fullParameterIdentifier)) {
            $recentValue = $this->memory->getParameter($fullParameterIdentifier);
        } else {
            $recentValue = '';
        }

        if ($parameter->getDefaultValue()) {
            if ($recentValue != '' && $recentValue != $parameter->getDefaultValue()) {
                $recentOutput = ', recent: ' . $recentValue;
            } else {
                $recentOutput = '';
            }
            $defaultString = ' [default: ' . $parameter->getDefaultValue() . $recentOutput . ']';
            $defaultValue = $parameter->getDefaultValue();
        } else {
            if ($recentValue) {
                $defaultString = ' [default: ' . $recentValue . ']';
                $defaultValue = $recentValue;
            } else {
                $defaultString = '';
                $defaultValue = '';
            }
        }

        return [
            'string' => $defaultString,
            'value' => $defaultValue
        ];
    }

    private function showCommandInformation(OutputInterface $output, Command $command): void
    {
        OutputHelper::writeWarningBox($this->output, $this->runWarning);

        $commands = CommandRunner::stringToMultilinePrompt($command->getPrompt());

        if (count($commands) > 1) {
            $plural = 's';
        } else {
            $plural = '';
        }

        $output->writeln('  Command' . $plural . ' to be run:');
        $output->writeln('');

        OutputHelper::writeInfoBox($output, $commands);

        $output->writeln('');
    }
}
