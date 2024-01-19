<?php

namespace App\Utils\Crawler\Trip\Model;

use App\Entity\Money;
use App\Utils\Crawler\Model\URLInterface;
use App\Utils\Enum\Food;

final readonly class Trip implements URLInterface
{
    public function __construct(
        private string $title,
        private string $url,
        private Food $food,
        private int $stars,
        private float $rate,
        private string $image,
        private \DateTimeInterface $from,
        private \DateTimeInterface $to,
        private Money $money,
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

    public function getFood(): Food
    {
        return $this->food;
    }

    public function getStars(): int
    {
        return $this->stars;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getFrom(): \DateTimeInterface
    {
        return $this->from;
    }

    public function getTo(): \DateTimeInterface
    {
        return $this->to;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }
}
