<?php

namespace App\Utils\Crawler\Trip;

use App\Entity\SourceInterface;
use App\Utils\Crawler\Trip\Model\Trip;
use App\Utils\Enum\Food;

interface TripInterface extends SourceInterface
{
    /**
     * @param Food[] $foods
     *
     * @return Trip[]
     */
    public function getTrips(
        string $place,
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        int $rangeFrom,
        int $rangeTo,
        array $foods = [],
        int $stars = null,
        float $rate = null,
        int $persons = 2
    ): array;
}
