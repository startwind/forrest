<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/vendor'
    ]);

    $rectorConfig->sets([
        \Rector\Set\ValueObject\DowngradeLevelSetList::DOWN_TO_PHP_74
    ]);
};
