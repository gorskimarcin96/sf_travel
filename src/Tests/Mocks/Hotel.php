<?php

namespace App\Tests\Mocks;

use App\Utils\Crawler\Hotel\HotelInterface;

final readonly class Hotel implements HotelInterface
{
    /** @param \App\Utils\Crawler\Hotel\Model\Hotel[] $data */
    public function __construct(private array $data = [])
    {
    }

    #[\Override]
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
    ): array {
        return $this->data;
    }

    #[\Override]
    public function getSource(): string
    {
        return self::class;
    }
}
