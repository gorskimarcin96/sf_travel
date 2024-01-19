<?php

namespace App\ApiResource\Input;

use App\Utils\Enum\Food;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class Search
{
    /**
     * @param Food[] $hotelFoods
     */
    public function __construct(
        #[Assert\NotBlank]
        private string $nation,
        #[Assert\NotBlank]
        private string $place,
        private \DateTimeImmutable $from,
        private \DateTimeImmutable $to,
        private int $adults = 2,
        private int $children = 0,
        #[Assert\Length(min: 3, max: 3)]
        private ?string $fromAirport = null,
        #[Assert\Length(min: 3, max: 3)]
        private ?string $toAirport = null,
        private array $hotelFoods = [],
        private ?int $hotelStars = null,
        private ?float $hotelRate = null,
        private ?int $rangeFrom = null,
        private ?int $rangeTo = null,
        private bool $force = false,
    ) {
    }

    public function getNation(): string
    {
        return $this->nation;
    }

    public function getPlace(): string
    {
        return $this->place;
    }

    public function getFrom(): \DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): \DateTimeImmutable
    {
        return $this->to;
    }

    public function getAdults(): int
    {
        return $this->adults;
    }

    public function getChildren(): int
    {
        return $this->children;
    }

    public function getFromAirport(): ?string
    {
        return $this->fromAirport;
    }

    public function getToAirport(): ?string
    {
        return $this->toAirport;
    }

    /**
     * @return Food[]
     */
    public function getHotelFoods(): array
    {
        return $this->hotelFoods;
    }

    public function getHotelStars(): ?int
    {
        return $this->hotelStars;
    }

    public function getHotelRate(): ?float
    {
        return $this->hotelRate;
    }

    public function getRangeFrom(): ?int
    {
        return $this->rangeFrom;
    }

    public function getRangeTo(): ?int
    {
        return $this->rangeTo;
    }

    public function isForce(): bool
    {
        return $this->force;
    }
}
