<?php

namespace Startwind\Forrest\Repository\Loader;

use Startwind\Forrest\Repository\RepositoryCollection;

class LocalPackageRepositoryLoader extends LocalJsonRepositoryLoader implements RepositoryLoader
{
    public const PACKAGE_FILE = 'package.json';

    private const LOCAL_REPOSITORY_IDENTIFIER = 'local-package-json';

    private const DEFAULT_LOCAL_REPOSITORY_NAME = 'Local package.json File';
    private const DEFAULT_LOCAL_DESCRIPTION = 'Commands from the local package.json file.';

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
            self::PACKAGE_FILE,
            'npm run ',
            self::DEFAULT_LOCAL_REPOSITORY_NAME,
            self::DEFAULT_LOCAL_DESCRIPTION,
            self::LOCAL_REPOSITORY_IDENTIFIER
        );
    }

    public static function isApplicable(): bool
    {
        return (file_exists(LocalPackageRepositoryLoader::PACKAGE_FILE));
    }
}
