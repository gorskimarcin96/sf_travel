<?php

namespace App\Entity\Traits;

use App\Utils\Enum\Currency;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait MoneyTrait
{
    #[ORM\Column]
    #[Groups(['optional-trips', 'hotels', 'flights', 'trips'])]
    private float $price;

    #[ORM\Column]
    #[Groups(['optional-trips', 'hotels', 'flights', 'trips'])]
    private bool $priceForOnePerson;

    #[ORM\Column(length: 3, enumType: Currency::class)]
    #[Groups(['optional-trips', 'hotels', 'flights', 'trips'])]
    private Currency $currency = Currency::PLN;

    public function getPrice(): float
    {
        return round($this->price * 100) / 100;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isPriceForOnePerson(): bool
    {
        return $this->priceForOnePerson;
    }

    public function setPriceForOnePerson(bool $priceForOnePerson): static
    {
        $this->priceForOnePerson = $priceForOnePerson;

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
