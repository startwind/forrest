<?php

namespace Startwind\Forrest\Repository;

use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Adapter\EditableAdapter;

class EditableFileRepository extends FileRepository implements EditableRepository
{
    /**
     * @inheritDoc
     */
    public function addCommand(Command $command): void
    {
        /** @var EditableAdapter $adapter */
        $adapter = $this->getAdapter();
        $adapter->addCommand($command);
    }

    /**
     * @inheritDoc
     */
    public function removeCommand(string $commandName): void
    {
        /** @var EditableAdapter $adapter */
        $adapter = $this->getAdapter();
        $adapter->removeCommand($commandName);
    }
}
