<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\FlightRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/** @codeCoverageIgnore */
#[ApiResource(operations: [new GetCollection()], normalizationContext: ['groups' => ['flights']])]
#[ApiFilter(SearchFilter::class, properties: ['search' => 'exact', 'source' => 'exact'])]
#[ORM\Entity(repositoryClass: FlightRepository::class)]
class Flight implements SourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue('SEQUENCE')]
    #[ORM\Column]
    #[Groups('flights')]
    private ?int $id = null;

    #[ORM\Column()]
    #[Groups('flights')]
    private string $fromAirport;

    #[ORM\Column()]
    #[Groups('flights')]
    private \DateTimeImmutable $fromStart;

    #[ORM\Column()]
    #[Groups('flights')]
    private \DateTimeImmutable $fromEnd;

    #[ORM\Column()]
    #[Groups('flights')]
    private int $fromStops;

    #[ORM\Column()]
    #[Groups('flights')]
    private string $toAirport;

    #[ORM\Column()]
    #[Groups('flights')]
    private \DateTimeImmutable $toStart;

    #[ORM\Column()]
    #[Groups('flights')]
    private \DateTimeImmutable $toEnd;

    #[ORM\Column()]
    #[Groups('flights')]
    private int $toStops;

    #[ORM\Column(length: 1000)]
    #[Groups('flights')]
    private string $url;

    #[ORM\OneToOne(cascade: ['all'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Groups('flights')]
    private Money $money;

    #[ORM\Column(length: 255)]
    #[Groups('flights')]
    private string $source;

    #[ORM\ManyToOne(inversedBy: 'flights')]
    private ?Search $search = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getFromAirport(): string
    {
        return $this->fromAirport;
    }

    public function setFromAirport(string $fromAirport): static
    {
        $this->fromAirport = $fromAirport;

        return $this;
    }

    public function getFromStart(): \DateTimeImmutable
    {
        return $this->fromStart;
    }

    public function setFromStart(\DateTimeImmutable $dateTimeImmutable): static
    {
        $this->fromStart = $dateTimeImmutable;

        return $this;
    }

    public function getFromEnd(): \DateTimeImmutable
    {
        return $this->fromEnd;
    }

    public function setFromEnd(\DateTimeImmutable $dateTimeImmutable): static
    {
        $this->fromEnd = $dateTimeImmutable;

        return $this;
    }

    public function getFromStops(): int
    {
        return $this->fromStops;
    }

    public function setFromStops(int $fromStops): static
    {
        $this->fromStops = $fromStops;

        return $this;
    }

    public function getToAirport(): string
    {
        return $this->toAirport;
    }

    public function setToAirport(string $toAirport): static
    {
        $this->toAirport = $toAirport;

        return $this;
    }

    public function getToStart(): \DateTimeImmutable
    {
        return $this->toStart;
    }

    public function setToStart(\DateTimeImmutable $dateTimeImmutable): static
    {
        $this->toStart = $dateTimeImmutable;

        return $this;
    }

    public function getToEnd(): \DateTimeImmutable
    {
        return $this->toEnd;
    }

    public function setToEnd(\DateTimeImmutable $dateTimeImmutable): static
    {
        $this->toEnd = $dateTimeImmutable;

        return $this;
    }

    public function getToStops(): int
    {
        return $this->toStops;
    }

    public function setToStops(int $toStops): static
    {
        $this->toStops = $toStops;

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

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function setMoney(Money $money): static
    {
        $this->money = $money;

        return $this;
    }

    #[\Override]
    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getSearch(): ?Search
    {
        return $this->search;
    }

    public function setSearch(?Search $search): static
    {
        $this->search = $search;

        return $this;
    }
}
