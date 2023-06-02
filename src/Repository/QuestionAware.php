<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Command\Command;

interface QuestionAware
{
    /**
     * Ask a question and get an answer with a prompt.
     */
    public function ask(string $question): array;
}
