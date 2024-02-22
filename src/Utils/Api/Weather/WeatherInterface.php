<?php

namespace App\Utils\Api\Weather;

use App\Entity\SourceInterface;
use App\Utils\Api\Weather\Model\Weather;

interface WeatherInterface extends SourceInterface
{
    /**
     * @return Weather[]
     */
    public function getByCityAndBetweenDate(string $city, \DateTimeInterface $from, \DateTimeInterface $to): array;
}
