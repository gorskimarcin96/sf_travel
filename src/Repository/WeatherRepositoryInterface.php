<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Weather;

interface WeatherRepositoryInterface
{
    /**
     * @return Weather[]
     */
    public function findByCityAndBetweenDateAndSource(
        City $city,
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        string $source
    ): array;
}
