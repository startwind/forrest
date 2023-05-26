<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Command\Command;

class EditableFileRepository extends FileRepository implements EditableRepository
{
    /**
     * @inheritDoc
     */
    public function addCommand(Command $command): void
    {
        $this->getAdapter()->addCommand($command);
    }

    /**
     * @inheritDoc
     */
    public function removeCommand(string $commandName): void
    {
        $this->getAdapter()->removeCommand($commandName);
    }
}
