<?php

namespace App\Entity;

use App\Repository\OptionalTripRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionalTripRepository::class)]
class OptionalTrip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    /**
     * @var string[]
     */
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::JSON)]
    private array $description = [];

    #[ORM\Column(length: 255, unique: true)]
    private string $url;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::TEXT)]
    private string $img;

    #[ORM\Column(length: 255)]
    private string $source;

    #[ORM\OneToOne(cascade: ['all'])]
    #[Orm\JoinColumn(onDelete: 'CASCADE')]
    private Money $money;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDescription(): array
    {
        return $this->description;
    }

    /**
     * @param string[] $description
     *
     * @return $this
     */
    public function setDescription(array $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getImg(): string
    {
        return $this->img;
    }

    public function setImg(string $img): static
    {
        $this->img = $img;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function setMoney(Money $money): static
    {
        $this->money = $money;

        return $this;
    }
}
