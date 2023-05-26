<?php

namespace Startwind\Forrest\Logger;

use Startwind\Forrest\Util\OutputHelper;
use Symfony\Component\Console\Output\OutputInterface;

class OutputLogger implements Logger
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function info($message): void
    {
        OutputHelper::writeInfoBox($this->output, $message);
    }

    public function error($message): void
    {
        OutputHelper::writeErrorBox($this->output, $message);
    }
}
