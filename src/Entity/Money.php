<?php

namespace App\Entity;

use App\Repository\MoneyRepository;
use App\Utils\Enum\Currency;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/** @codeCoverageIgnore */
#[ORM\Entity(repositoryClass: MoneyRepository::class)]
class Money
{
    #[ORM\Id]
    #[ORM\GeneratedValue('SEQUENCE')]
    #[ORM\Column]
    #[Groups(['optional-trips', 'hotels', 'flights', 'trips'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['optional-trips', 'hotels', 'flights', 'trips'])]
    private float $price;

    #[ORM\Column(length: 3, enumType: Currency::class)]
    #[Groups(['optional-trips', 'hotels', 'flights', 'trips'])]
    private Currency $currency = Currency::PLN;

    public function getId(): ?int
    {
        return $this->id;
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
