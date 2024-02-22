<?php

namespace App\Utils\Crawler\Hotel;

use App\Entity\SourceInterface;
use App\Utils\Enum\Food;

interface HotelInterface extends SourceInterface
{
    /**
     * @param Food[] $foods
     *
     * @return Model\Hotel[]
     */
    public function getHotels(
        string $place,
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        int $rangeFrom,
        int $rangeTo,
        array $foods = [],
        int $stars = null,
        float $rate = null,
        int $adults = 2,
        int $children = 0,
    ): array;
}
