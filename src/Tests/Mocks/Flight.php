<?php

namespace App\Tests\Mocks;

use App\Utils\Crawler\Flight\FlightInterface;

final readonly class Flight implements FlightInterface
{
    /** @param \App\Utils\Crawler\Flight\Model\Flight[] $data */
    public function __construct(private array $data = [])
    {
    }

    #[\Override]
    public function getFlights(
        string $fromAirport,
        string $toAirport,
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        int $adults = 2,
        int $children = 0
    ): array {
        return $this->data;
    }

    #[\Override]
    public function restartPantherClient(): void
    {
    }

    #[\Override]
    public function getSource(): string
    {
        return self::class;
    }
}
