<?php

namespace Startwind\Forrest\Command\Answer;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Command\CommandFactory;

class Answer
{
    private Command $command;
    private string $question;
    private string $answer;

    public function __construct(string|array $prompt, string $question, string $answer)
    {
        if (is_string($prompt)) {
            $this->command = CommandFactory::fromArray([
                CommandFactory::CONFIG_FIELD_NAME => 'forrest-ai',
                CommandFactory::CONFIG_FIELD_DESCRIPTION => 'Answer to: ' . $question,
                CommandFactory::CONFIG_FIELD_PROMPT => $prompt
            ]);
        } else {
            $this->command = CommandFactory::fromArray($prompt);
        }

        $this->question = $question;
        $this->answer = $answer;
    }

    public function getCommand(): Command
    {
        return $this->command;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }
}
