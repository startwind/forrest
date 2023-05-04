<?php

namespace Startwind\Forrest\Util;

use Symfony\Component\Console\Output\OutputInterface;

abstract class OutputHelper
{
    /**
     * Show a blue output box with a info message.
     */
    public static function writeInfoBox(OutputInterface $output, string|array $message): void
    {
        if (is_array($message) && empty($message)) {
            return;
        }
        $maxLength = 0;

        if (!is_array($message)) {
            $message = [$message];
        }

        foreach ($message as $singleMessage) {
            $maxLength = max($maxLength, strlen($singleMessage));
        }

        $spaces = self::getSpaces($message[0]);

        $output->writeln("");
        $output->writeln('<bg=cyan>' . self::getPreparedMessage('', $maxLength, 4) . "</>");

        foreach ($message as $singleMessage) {
            $output->writeln("<bg=cyan>  " . self::getPreparedMessage($singleMessage, $maxLength, 2) . "</>");
        }

        $output->writeln('<bg=cyan>' . $spaces . "</>");
        $output->writeln("");
    }

    /**
     * Show a red output box with a warning message.
     */
    public static function writeErrorBox(OutputInterface $output, string|array $message): void
    {
        $maxLength = 0;

        if (!is_array($message)) {
            $message = [$message];
        }

        foreach ($message as $singleMessage) {
            $maxLength = max($maxLength, strlen($singleMessage));
        }

        $spaces = self::getSpaces($message[0]);

        $output->writeln("");
        $output->writeln('<error>' . self::getPreparedMessage('', $maxLength, 4) . "</error>");

        foreach ($message as $singleMessage) {
            $output->writeln("<error>  " . self::getPreparedMessage($singleMessage, $maxLength, 2) . "</error>");
        }

        $output->writeln('<error>' . $spaces . "</error>");
        $output->writeln("");
    }

    /**
     * Add whitespaces to the message of needed to fit to the box.
     */
    private static function getPreparedMessage(string $message, int $maxLength, int $additionalSpaces = 0): string
    {
        return $message . str_repeat(' ', $maxLength - strlen($message) + $additionalSpaces);
    }

    /**
     * Fill out the spaces in the trailing empty lines in the box.
     */
    private static function getSpaces(string $message): string
    {
        return str_repeat(' ', strlen($message) + 4);
    }
}
