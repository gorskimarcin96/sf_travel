<?php

namespace App\Entity;

use App\Repository\MoneyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MoneyRepository::class)]
class Money
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['optional-trips', 'hotels'])]
    private ?int $id = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['optional-trips', 'hotels'])]
    private float $price;

    #[ORM\Column(length: 3)]
    #[Groups(['optional-trips', 'hotels'])]
    private string $currency = 'PLN';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }
}
