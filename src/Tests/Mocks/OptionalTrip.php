<?php

namespace App\Tests\Mocks;

use App\Utils\Crawler\OptionalTrip\OptionalTripInterface;

final readonly class OptionalTrip implements OptionalTripInterface
{
    /** @param \App\Utils\Crawler\OptionalTrip\Model\OptionalTrip[] $data */
    public function __construct(private array $data = [])
    {
    }

    /** @return \App\Utils\Crawler\OptionalTrip\Model\OptionalTrip[] */
    #[\Override]
    public function getOptionalTrips(string $place, ?string $nation = null): array
    {
        return $this->data;
    }

    #[\Override]
    public function getSource(): string
    {
        return self::class;
    }

    #[\Override]
    public function restartPantherClient(): void
    {
    }
}
