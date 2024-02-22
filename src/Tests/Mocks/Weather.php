<?php

namespace App\Tests\Mocks;

use App\Utils\Api\Weather\WeatherInterface;

final readonly class Weather implements WeatherInterface
{
    /** @param \App\Utils\Api\Weather\Model\Weather[] $data */
    public function __construct(private array $data = [])
    {
    }

    #[\Override]
    public function getSource(): string
    {
        return self::class;
    }

    #[\Override]
    public function getByCityAndBetweenDate(
        string $city,
        \DateTimeInterface $from,
        \DateTimeInterface $to
    ): array {
        return $this->data;
    }
}
