<?php

namespace Startwind\Forrest\Logger;

class ForrestLogger
{
    /**
     * @var Logger[]
     */
    private static array $logger = [];

    public static function addLogger(Logger $logger): void
    {
        self::$logger[] = $logger;
    }

    public static function info($message): void
    {
        foreach (self::$logger as $logger) {
            $logger->info($message);
        }
    }

    public static function error($message): void
    {
        foreach (self::$logger as $logger) {
            $logger->error($message);
        }
    }
}
