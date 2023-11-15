<?php

namespace App\Utils\Crawler;

use Symfony\Component\Panther\Client;

abstract readonly class PantherClient
{
    public function __construct(protected Client $client)
    {
    }

    public function restartPantherClient(): void
    {
        $this->client->restart();
    }
}
