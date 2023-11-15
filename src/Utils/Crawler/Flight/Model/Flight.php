<?php

namespace App\Utils\Crawler\Flight\Model;

use App\Entity\Money;
use App\Utils\Crawler\Model\URLInterface;

final readonly class Flight implements URLInterface
{
    public function __construct(
        private string $fromAirport,
        private \DateTimeImmutable $fromStart,
        private \DateTimeImmutable $fromEnd,
        private int $fromStops,
        private string $toAirport,
        private \DateTimeImmutable $toStart,
        private \DateTimeImmutable $toEnd,
        private int $toStops,
        private Money $money,
        private string $url,
    ) {
    }

    public function getFromAirport(): string
    {
        return $this->fromAirport;
    }

    public function getFromStart(): \DateTimeImmutable
    {
        return $this->fromStart;
    }

    public function getFromEnd(): \DateTimeImmutable
    {
        return $this->fromEnd;
    }

    public function getFromStops(): int
    {
        return $this->fromStops;
    }

    public function getToAirport(): string
    {
        return $this->toAirport;
    }

    public function getToStart(): \DateTimeImmutable
    {
        return $this->toStart;
    }

    public function getToEnd(): \DateTimeImmutable
    {
        return $this->toEnd;
    }

    public function getToStops(): int
    {
        return $this->toStops;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
