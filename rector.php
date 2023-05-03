<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/vendor'
    ]);

    $rectorConfig->sets([
        \Rector\Set\ValueObject\DowngradeLevelSetList::DOWN_TO_PHP_74
    ]);
};
