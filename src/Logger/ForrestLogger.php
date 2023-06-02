<?php

namespace Startwind\Forrest\Logger;

class ForrestLogger
{
    public const LEVEL_ERROR = 100;
    public const LEVEL_WARN = 200;
    public const LEVEL_INFO = 300;

    /**
     * @var Logger[]
     */
    private static array $logger = [];

    private static int $logLevel = self::LEVEL_ERROR;

    private static array $levels = [
        self::LEVEL_ERROR,
        self::LEVEL_WARN,
        self::LEVEL_INFO
    ];

    public static function setLogLevel(int $logLevel): void
    {
        if (!in_array($logLevel, self::$levels)) {
            throw new \RuntimeException('The given log level (' . $logLevel . ') does not exists.');
        }
        self::$logLevel = $logLevel;
    }

    public static function addLogger($key, Logger $logger): void
    {
        self::$logger[$key] = $logger;
    }

    public static function info($message): void
    {
        if (self::$logLevel < self::LEVEL_INFO) {
            return;
        }
        foreach (self::$logger as $logger) {
            $logger->info($message);
        }
    }

    public static function warn($message): void
    {
        if (self::$logLevel < self::LEVEL_WARN) {
            return;
        }
        foreach (self::$logger as $logger) {
            $logger->warn($message);
        }
    }

    public static function error($message): void
    {
        if (self::$logLevel < self::LEVEL_ERROR) {
            return;
        }
        foreach (self::$logger as $logger) {
            $logger->error($message);
        }
    }
}
