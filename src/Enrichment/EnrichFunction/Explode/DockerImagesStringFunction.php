<?php

namespace Startwind\Forrest\Enrichment\EnrichFunction\Explode;

use Startwind\Forrest\Enrichment\EnrichFunction\CacheableFunction;
use Startwind\Forrest\Logger\ForrestLogger;
use Startwind\Forrest\Runner\CommandRunner;
use Startwind\Forrest\Runner\Exception\ToolNotFoundException;
use Symfony\Component\Console\Command\Command;

class DockerImagesStringFunction extends BasicExplodeFunction implements CacheableFunction
{
    protected string $functionName = 'docker-images';

    protected function getValue(string $value): array
    {
        if (!CommandRunner::isToolInstalled('docker', $command)) {
            throw new ToolNotFoundException('The cli tool "docker" has to be installed to use the "docker-name" enrichment function.');
        }

        exec("docker ps --no-trunc --format='{{json .}}' 2>&1", $output, $statusCode);

        if ($statusCode !== Command::SUCCESS) {
            if (str_contains($output[0], 'Is the docker daemon running?')) {
                ForrestLogger::warn('Docker daemon not running. Please start it to use the "docker-names" function.');
            } else {
                ForrestLogger::warn($output[0]);
            }
            return [];
        }

        $names = [];

        foreach ($output as $containerJson) {
            $container = json_decode($containerJson, true);
            if ($container) {
                $names[] = $container['Image'];
            }
        }

        if (count($names) == 0) {
            ForrestLogger::warn('Currently there are no docker containers running. Please start one to use the "docker-images" function.');
        }

        return $names;
    }
}
