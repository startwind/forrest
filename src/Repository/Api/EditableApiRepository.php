<?php

namespace Startwind\Forrest\Repository\Api;

use GuzzleHttp\RequestOptions;
use Startwind\Forrest\Command\Command;
use Startwind\Forrest\Logger\ForrestLogger;
use Startwind\Forrest\Repository\EditableRepository;

class EditableApiRepository extends ApiRepository implements EditableRepository
{
    private string $password = '';

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function addCommand(Command $command): void
    {
        $response = $this->client->post(
            $this->endpoint . 'command/' . $command->getName(),
            [
                RequestOptions::JSON => $command,
                RequestOptions::HEADERS => [
                    'Authorization' => $this->password
                ],
                'verify' => false
            ]
        );

        $plainResponse = (string)$response->getBody();
        $response = json_decode($plainResponse, true);

        if (!$response) {
            throw new \RuntimeException('The APIs response was not a valid JSON string. Body was: ' . $plainResponse);
        }

        if ($response['status'] == 'failure') {
            ForrestLogger::error($response['message']);
        }
    }

    public function removeCommand(string $commandName): void
    {
        // TODO: Implement removeCommand() method.
    }
}
