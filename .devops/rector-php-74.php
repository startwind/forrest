<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\DowngradeLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/../src',
        __DIR__ . '/../vendor',
        __DIR__ . '/../bin'
    ]);

    $rectorConfig->sets([
        DowngradeLevelSetList::DOWN_TO_PHP_74
    ]);
};
