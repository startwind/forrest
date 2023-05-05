<?php

namespace Startwind\Forrest\Util;

use Symfony\Component\Console\Output\OutputInterface;

abstract class OSHelper
{
    /**
     * Return true if the user is root.
     *
     * Does not work on Windows machines yet.
     */
    public static function isRoot(): bool
    {
        if (function_exists('posix_getuid')) {
            return posix_getuid() === 0;
        } else {
            return false;
        }
    }
}
