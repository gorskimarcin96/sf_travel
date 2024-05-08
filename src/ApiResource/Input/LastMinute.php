<?php

namespace App\ApiResource\Input;

use App\Utils\Enum\Food;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class LastMinute
{
    /**
     * @param Food[] $hotelFoods
     */
    public function __construct(
        private ?\DateTimeImmutable $from,
        private ?\DateTimeImmutable $to,
        private int $adults = 2,
        private int $children = 0,
        #[Assert\Length(min: 3, max: 3)]
        private ?string $fromAirport = null,
        private array $hotelFoods = [],
        private ?int $hotelStars = null,
        private ?float $hotelRate = null,
        private ?int $rangeFrom = null,
        private ?int $rangeTo = null,
        private bool $force = false,
    ) {
    }

    public function getFrom(): ?\DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): ?\DateTimeImmutable
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
