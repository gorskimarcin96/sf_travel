<?php

namespace App\Utils\Api\Weather\Model;

final readonly class Weather
{
    public function __construct(
        private \DateTimeInterface $date,
        private float $temperature2mMean,
        private float $precipitationHours,
        private float $precipitationSum,
    ) {
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getTemperature2mMean(): float
    {
        return $this->temperature2mMean;
    }

    public function getPrecipitationHours(): float
    {
        return $this->precipitationHours;
    }

    public function getPrecipitationSum(): float
    {
        return $this->precipitationSum;
    }
}
