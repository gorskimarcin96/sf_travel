<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\HttpOperation;
use App\Controller\SearchController;
use App\Repository\SearchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(operations: [
    new HttpOperation(
        method: 'POST',
        uriTemplate: '/search',
        status: Response::HTTP_OK,
        controller: SearchController::class,
        input: \App\ApiResource\Input\Search::class
    ),
    new Get(uriTemplate: '/search/{id}'),
    new GetCollection(uriTemplate: '/search', normalizationContext: ['groups' => ['search_collection']]),
], normalizationContext: ['groups' => ['search']])]
#[ORM\Entity(repositoryClass: SearchRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Search
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['search', 'search_collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['search', 'search_collection'])]
    private string $nation;

    #[ORM\Column(length: 255)]
    #[Groups(['search', 'search_collection'])]
    private string $place;

    #[ORM\Column(name: 'from_at', type: \Doctrine\DBAL\Types\Types::DATE_IMMUTABLE, length: 255)]
    #[Groups(['search', 'search_collection'])]
    private \DateTimeImmutable $from;

    #[ORM\Column(name: 'to_at', type: \Doctrine\DBAL\Types\Types::DATE_IMMUTABLE, length: 255)]
    #[Groups(['search', 'search_collection'])]
    private \DateTimeImmutable $to;

    #[ORM\Column(length: 3, nullable: true)]
    #[Groups(['search', 'search_collection'])]
    private ?string $fromAirport = null;

    #[ORM\Column(length: 3, nullable: true)]
    #[Groups(['search', 'search_collection'])]
    private ?string $toAirport = null;

    #[ORM\Column]
    #[Groups(['search', 'search_collection'])]
    private int $adults;

    #[ORM\Column]
    #[Groups(['search', 'search_collection'])]
    private int $children;

    /**
     * @var string[]
     */
    #[ORM\Column()]
    #[Groups(['search', 'search_collection'])]
    private array $services = [];

    /**
     * @var string[]
     */
    #[ORM\Column()]
    private array $todo = [];

    /**
     * @var array<array<string>|string>
     */
    #[ORM\Column()]
    #[Groups(['search', 'search_collection'])]
    private array $errors = [];

    #[ORM\Column]
    #[Groups(['search', 'search_collection'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['search', 'search_collection'])]
    private \DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, OptionalTrip>|OptionalTrip[]
     */
    #[ORM\OneToMany(mappedBy: 'search', targetEntity: OptionalTrip::class)]
    private Collection $optionalTrips;

    /**
     * @var Collection<int, TripPage>|TripPage[]
     */
    #[ORM\OneToMany(mappedBy: 'search', targetEntity: TripPage::class)]
    private Collection $tripPages;

    /**
     * @var Collection<int, Hotel>|Hotel[]
     */
    #[ORM\OneToMany(mappedBy: 'search', targetEntity: Hotel::class)]
    private Collection $hotels;

    /**
     * @var Collection<int, Flight>|Flight[]
     */
    #[ORM\OneToMany(mappedBy: 'search', targetEntity: Flight::class)]
    private Collection $flights;

    public function __construct()
    {
        $this->optionalTrips = new ArrayCollection();
        $this->tripPages = new ArrayCollection();
        $this->hotels = new ArrayCollection();
        $this->flights = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNation(): string
    {
        return $this->nation;
    }

    public function setNation(string $nation): static
    {
        $this->nation = strtolower($nation);

        return $this;
    }

    public function getPlace(): string
    {
        return $this->place;
    }

    public function setPlace(string $place): static
    {
        $this->place = strtolower($place);

        return $this;
    }

    public function getFrom(): \DateTimeImmutable
    {
        return $this->from;
    }

    public function setFrom(\DateTimeImmutable $dateTimeImmutable): static
    {
        $this->from = $dateTimeImmutable;

        return $this;
    }

    public function getTo(): \DateTimeImmutable
    {
        return $this->to;
    }

    public function setTo(\DateTimeImmutable $dateTimeImmutable): static
    {
        $this->to = $dateTimeImmutable;

        return $this;
    }

    public function getFromAirport(): ?string
    {
        return $this->fromAirport;
    }

    public function setFromAirport(?string $fromAirport): static
    {
        $this->fromAirport = $fromAirport;

        return $this;
    }

    public function getToAirport(): ?string
    {
        return $this->toAirport;
    }

    public function setToAirport(?string $toAirport): static
    {
        $this->toAirport = $toAirport;

        return $this;
    }

    /**
     * @return Collection<int, Flight>
     */
    public function getFlight(): Collection
    {
        return $this->flights;
    }

    /**
     * @param Collection<int, Flight> $flights
     */
    public function setFlight(Collection $flights): static
    {
        $this->flights = $flights;

        return $this;
    }

    public function getAdults(): int
    {
        return $this->adults;
    }

    public function setAdults(int $adults): static
    {
        $this->adults = $adults;

        return $this;
    }

    public function getChildren(): int
    {
        return $this->children;
    }

    public function setChildren(int $children): static
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    public function addService(string $service): static
    {
        $this->services[] = $service;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTodo(): array
    {
        return $this->todo;
    }

    /**
     * @param string[] $todo
     */
    public function setTodo(array $todo): static
    {
        $this->todo = $todo;

        return $this;
    }

    /**
     * @return array<array<string>|string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string[] $errors
     */
    public function addError(string $searchServiceClass, array $errors): static
    {
        $this->errors[$searchServiceClass] = $errors;

        return $this;
    }

    #[Groups(['search', 'search_collection'])]
    public function isFinished(): bool
    {
        return [] === $this->todo;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist()]
    public function updateCreatedAt(): void
    {
        if (null === $this->id) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }
    }

    #[ORM\PrePersist()]
    #[ORM\PreUpdate()]
    public function updateUpdatedAt(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    /**
     * @return Collection<int, OptionalTrip>
     */
    public function getOptionalTrips(): Collection
    {
        return $this->optionalTrips;
    }

    public function addOptionalTrip(OptionalTrip $optionalTrip): static
    {
        if (!$this->optionalTrips->contains($optionalTrip)) {
            $this->optionalTrips->add($optionalTrip);
            $optionalTrip->setSearch($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, TripPage>
     */
    public function getTripPages(): Collection
    {
        return $this->tripPages;
    }

    public function addTripPage(TripPage $tripPage): static
    {
        if (!$this->tripPages->contains($tripPage)) {
            $this->tripPages->add($tripPage);
            $tripPage->setSearch($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Hotel>
     */
    public function getHotels(): Collection
    {
        return $this->hotels;
    }

    public function addHotel(Hotel $hotel): static
    {
        if (!$this->hotels->contains($hotel)) {
            $this->hotels->add($hotel);
            $hotel->setSearch($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Flight>
     */
    public function getFlights(): Collection
    {
        return $this->flights;
    }

    public function addFlight(Flight $flight): static
    {
        if (!$this->flights->contains($flight)) {
            $this->flights->add($flight);
            $flight->setSearch($this);
        }

        return $this;
    }

    public function getServiceTodo(): ?string
    {
        return array_pop($this->todo);
    }

    /**
     * @return array<string, int>
     */
    #[Groups(['search', 'search_collection'])]
    public function getCountServices(): array
    {
        return array_count_values([
            ...$this->optionalTrips->map(fn (SourceInterface $source): string => $source->getSource())->toArray(),
            ...$this->tripPages->map(fn (SourceInterface $source): string => $source->getSource())->toArray(),
            ...$this->hotels->map(fn (SourceInterface $source): string => $source->getSource())->toArray(),
            ...$this->flights->map(fn (SourceInterface $source): string => $source->getSource())->toArray(),
        ]);
    }
}
