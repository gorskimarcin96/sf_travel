<?php

namespace App\Utils\Crawler\Common;

use App\Utils\Enum\Currency;

final readonly class Money
{
    public function __construct(private float $price, private bool $priceForOnePerson, private Currency $currency = Currency::PLN)
    {
    }

    public function getPrice(): float
    {
        return round($this->price * 100) / 100;
    }

    public function isPriceForOnePerson(): bool
    {
        return $this->priceForOnePerson;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
