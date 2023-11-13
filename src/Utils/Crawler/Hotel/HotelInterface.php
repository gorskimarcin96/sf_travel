<?php

namespace App\Utils\Crawler\Hotel;

use App\Entity\SourceInterface;

interface HotelInterface extends SourceInterface
{
    /**
     * @return Model\Hotel[]
     */
    public function getHotels(string $place, \DateTimeImmutable $from, \DateTimeImmutable $to, int $adults = 2, int $children = 0): array;
}
