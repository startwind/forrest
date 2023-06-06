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
        self::writeMessage($output, $message, '<fg=black;bg=cyan>', '</></>');
    }

    /**
     * Show a red output box with a error message.
     */
    public static function writeErrorBox(OutputInterface $output, string|array $message): void
    {
        self::writeMessage($output, $message, '<error>', '</error>');
    }

    /**
     * Show a red output box with a warning message.
     */
    public static function writeWarningBox(OutputInterface $output, string|array $message): void
    {
        self::writeMessage($output, $message, '<fg=black;bg=yellow>', '</>');
    }

    private static function writeMessage(OutputInterface $output, string|array $message, string $prefix = '', string $postfix = ''): void
    {
        $maxLength = 0;

        $messages = self::prepareMessages($message);

        foreach ($messages as $singleMessage) {
            $maxLength = max($maxLength, strlen($singleMessage));
        }

        $output->writeln("");

        foreach ($messages as $singleMessage) {
            $output->writeln($prefix . "  " . self::getPreparedMessage($singleMessage, $maxLength, 2) . $postfix);
        }

        $output->writeln("");
    }

    private static function prepareMessages(string|array $message): array
    {
        if (!is_array($message)) {
            $message = [$message];
        }

        array_unshift($message, '');
        $message[] = '';

        return $message;
    }

    /**
     * Add whitespaces to the message of needed to fit to the box.
     */
    private static function getPreparedMessage(string $message, int $maxLength, int $additionalSpaces = 0): string
    {
        return $message . str_repeat(' ', $maxLength - strlen($message) + $additionalSpaces);
    }

    public static function indentText(string $text, int $indent = 2, int $width = 100, $prefix = ''): array
    {
        $wrapped = explode("\n", wordwrap($text, $width));

        $result = [];

        foreach ($wrapped as $line) {
            $result[] = $prefix . str_repeat(' ', $indent) . $line;
        }

        return $result;
    }
}
