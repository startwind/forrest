<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Command\Command;

interface StatusAwareRepository extends Repository
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILURE = 'failure';

    public function pushStatus(string $commandIdentifier, string $status): void;
}
