<?php

namespace App\Utils\Crawler\Common;

use App\Utils\Enum\Currency;

class Money
{
    public function __construct(private float $price, private Currency $currency = Currency::PLN)
    {
    }

    public function getPrice(): float
    {
        return round($this->price * 100) / 100;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }
}