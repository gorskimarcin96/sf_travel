<?php

namespace App\Utils\Crawler\OptionalTrip;

use Symfony\Component\Panther\Client;

abstract readonly class AbstractOptionalTrip
{
    public function __construct(protected Client $client)
    {
    }

    public function restartPantherClient(): void
    {
        $this->client->restart();
    }
}
