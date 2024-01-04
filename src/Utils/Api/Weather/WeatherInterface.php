<?php

namespace App\Utils\Api\Weather;

use App\Utils\Api\Weather\Model\Weather;

interface WeatherInterface
{
    /**
     * @return Weather[]
     */
    public function getByCityAndBetweenDate(string $city, \DateTimeInterface $from, \DateTimeInterface $to): array;
}
