<?php

namespace App\Utils\Crawler\Flight;

use App\Entity\SourceInterface;

interface FlightInterface extends SourceInterface
{
    /**
     * @return Model\Flight[]
     */
    public function getFlights(
        string $fromAirport,
        string $toAirport,
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        int $adults = 2,
        int $children = 0
    ): array;

    public function restartPantherClient(): void;
}
