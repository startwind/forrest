<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction\Explode;

use Startwind\Forrest\Runner\CommandRunner;
use Startwind\Forrest\Runner\Exception\ToolNotFoundException;
use Symfony\Component\Console\Command\Command;

class DockerImagesStringFunction extends BasicExplodeFunction
{
    protected string $functionName = 'docker-names';

    protected function getValue(string $value): array
    {
        if (!CommandRunner::isToolInstalled('docker', $command)) {
            throw new ToolNotFoundException('The cli tool "docker" has to be installed to use the "docker-name" enrichment function.');
        }

        exec("docker ps --no-trunc --format='{{json .}}' 2>&1", $output, $statusCode);

        if ($statusCode !== Command::SUCCESS) {
            throw new \RuntimeException($output[0]);
        }

        $names = [];

        foreach ($output as $containerJson) {
            $container = json_decode($containerJson, true);
            if ($container) {
                $names[] = $container['Names'];
            }
        }

        return $names;
    }

}
