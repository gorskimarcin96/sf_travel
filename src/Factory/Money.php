<?php

namespace App\Factory;

use App\Utils\Enum\Currency;

final class Money
{
    public static function create(float $price, Currency $currency = Currency::PLN): \App\Entity\Money
    {
        return (new \App\Entity\Money())
            ->setPrice($price)
            ->setCurrency($currency);
    }
}
