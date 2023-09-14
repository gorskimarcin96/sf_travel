<?php

namespace App\Utils\Crawler\OptionalTrip\Model;

use App\Entity\Money;

final readonly class OptionalTrip
{
    /** @param string|string[] $description */
    public function __construct(
        private string $title,
        private string|array $description,
        private string $url,
        private string $img,
        private Money $money,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /** @return string[] */
    public function getDescription(): array
    {
        return is_string($this->description) ? [$this->description] : $this->description;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getImg(): string
    {
        return $this->img;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }
}
