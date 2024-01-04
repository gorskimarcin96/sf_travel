<?php

namespace App\Utils\Api\Geocoding\Model;

final readonly class Geocoding
{
    public function __construct(
        private int $id,
        private string $name,
        private float $latitude,
        private float $longitude,
        private string $countryCode,
        private string $country
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
