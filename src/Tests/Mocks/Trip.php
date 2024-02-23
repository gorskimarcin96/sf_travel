<?php

namespace App\Tests\Mocks;

use App\Utils\Crawler\Trip\TripInterface;

final class Trip implements TripInterface
{
    /** @param \App\Utils\Crawler\Trip\Model\Trip[] $data */
    public function __construct(private array $data = [])
    {
    }

    #[\Override]
    public function getSource(): string
    {
        return self::class;
    }

    #[\Override]
    public function getTrips(
        string $place,
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        int $rangeFrom,
        int $rangeTo,
        array $foods = [],
        ?int $stars = null,
        ?float $rate = null,
        int $persons = 2
    ): array {
        return $this->data;
    }
}
