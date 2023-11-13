<?php

namespace App\Utils\Crawler\Hotel\Model;

use App\Entity\Money;
use App\Utils\Crawler\Model\URLInterface;

final readonly class Hotel implements URLInterface
{
    /**
     * @param string[] $descriptions
     */
    public function __construct(
        private string $title,
        private string $url,
        private string $image,
        private string $address,
        private array $descriptions,
        private Money $money,
        private ?float $rate
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string[]
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }
}
