<?php

namespace Startwind\Forrest\Repository\Loader;

use Startwind\Forrest\Repository\RepositoryCollection;

class LocalComposerRepositoryLoader extends LocalJsonRepositoryLoader implements RepositoryLoader
{
    public const COMPOSER_FILE = 'composer.json';

    private const LOCAL_REPOSITORY_IDENTIFIER = 'local-composer';

    private const DEFAULT_LOCAL_REPOSITORY_NAME = 'Local Composer File';
    private const DEFAULT_LOCAL_DESCRIPTION = 'Commands from the local composer file.';

    /**
     * @inheritDoc
     */
    public function getIdentifiers(): array
    {
        return [self::LOCAL_REPOSITORY_IDENTIFIER];
    }

    /**
     * @inheritDoc
     */
    public function enrich(RepositoryCollection $repositoryCollection): void
    {
        $this->enrichWithFile(
            $repositoryCollection,
            self::COMPOSER_FILE,
            'composer run ',
            self::DEFAULT_LOCAL_REPOSITORY_NAME,
            self::DEFAULT_LOCAL_DESCRIPTION,
            self::LOCAL_REPOSITORY_IDENTIFIER
        );
    }

    public static function isApplicable(): bool
    {
        return (file_exists(LocalComposerRepositoryLoader::COMPOSER_FILE));
    }
}
