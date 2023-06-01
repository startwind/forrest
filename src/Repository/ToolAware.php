<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Command\Tool\Tool;

interface ToolAware extends Repository
{
    public function findToolInformation(string $tool): Tool|bool;
}
