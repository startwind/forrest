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

    /**
     * @throws \Exception
     */
    public static function copyToClipboard(string $string): bool
    {
        if (PHP_OS_FAMILY === "Windows") {
            // works on windows 7 +
            $clip = popen("clip", "wb");
        } elseif (PHP_OS_FAMILY === "Linux") {
            return false;
            // tested, works on ArchLinux
            // $clip = popen('xclip -selection clipboard', 'wb');
        } elseif (PHP_OS_FAMILY === "Darwin") {
            // untested!
            $clip = popen('pbcopy', 'wb');
        } else {
            throw new \Exception("running on unsupported OS: " . PHP_OS_FAMILY . " - only Windows, Linux, and MacOS supported.");
        }
        $written = fwrite($clip, $string);
        return (pclose($clip) === 0 && strlen($string) === $written);
    }

    public static function getOS(): array
    {
        if (PHP_OS_FAMILY === "Darwin") {
            return ['name' => 'MacOS', 'version' => ''];
        }

        return ['name' => PHP_OS_FAMILY, 'version' => ''];
    }
}
