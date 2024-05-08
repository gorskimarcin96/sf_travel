<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\HttpOperation;
use App\Controller\LastMinuteController;
use App\Repository\LastMinuteRepository;
use App\Utils\Enum\Food;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(operations: [
    new HttpOperation(
        method: 'POST',
        uriTemplate: '/last-minute',
        status: Response::HTTP_OK,
        controller: LastMinuteController::class,
        input: \App\ApiResource\Input\LastMinute::class
    ),
    new Get(uriTemplate: '/last-minute/{id}'),
    new GetCollection(uriTemplate: '/last-minute', normalizationContext: ['groups' => ['last_minute_collection']]),
], normalizationContext: ['groups' => ['last_minute']])]
#[ORM\Entity(repositoryClass: LastMinuteRepository::class)]
#[ORM\HasLifecycleCallbacks]
class LastMinute
{
    #[ORM\Id]
    #[ORM\GeneratedValue('SEQUENCE')]
    #[ORM\Column]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private ?int $id = null;

    #[ORM\Column(name: 'from_at', type: \Doctrine\DBAL\Types\Types::DATE_IMMUTABLE, length: 255, nullable: true)]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private ?\DateTimeImmutable $from = null;

    #[ORM\Column(name: 'to_at', type: \Doctrine\DBAL\Types\Types::DATE_IMMUTABLE, length: 255, nullable: true)]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private ?\DateTimeImmutable $to = null;

    #[ORM\Column]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private int $adults;

    #[ORM\Column]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private int $children;

    #[ORM\Column(length: 3, nullable: true)]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private ?string $fromAirport = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private ?int $rangeFrom = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private ?int $rangeTo = null;

    /**
     * @var Food[]
     */
    #[ORM\Column(enumType: Food::class, options: ['jsonb' => true])]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private array $hotelFoods = [];

    #[ORM\Column(nullable: true)]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private ?int $hotelStars = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private ?float $hotelRate = null;

    /**
     * @var string[]
     */
    #[ORM\Column()]
    #[Groups(['last_minute', 'last_minute_collection'])]
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
    #[Groups(['last_minute', 'last_minute_collection'])]
    private array $errors = [];

    #[ORM\Column]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['last_minute', 'last_minute_collection'])]
    private \DateTimeImmutable $updatedAt;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, Trip>|Trip[]
     */
    #[ORM\OneToMany(mappedBy: 'lastMinute', targetEntity: Trip::class)]
    private Collection $trips;

    public function __construct()
    {
        $this->trips = new ArrayCollection();
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

    public function getFrom(): ?\DateTimeImmutable
    {
        return $this->from;
    }

    public function setFrom(?\DateTimeImmutable $dateTimeImmutable): static
    {
        $this->from = $dateTimeImmutable;

        return $this;
    }

    public function getTo(): ?\DateTimeImmutable
    {
        return $this->to;
    }

    public function setTo(?\DateTimeImmutable $dateTimeImmutable): static
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

    public function getRangeFrom(): ?int
    {
        return $this->rangeFrom;
    }

    public function setRangeFrom(?int $rangeFrom): static
    {
        $this->rangeFrom = $rangeFrom;

        return $this;
    }

    public function getRangeTo(): ?int
    {
        return $this->rangeTo;
    }

    public function setRangeTo(?int $rangeTo): static
    {
        $this->rangeTo = $rangeTo;

        return $this;
    }

    /**
     * @return Food[]
     */
    public function getHotelFoods(): array
    {
        return $this->hotelFoods;
    }

    /**
     * @param Food[] $hotelFoods
     */
    public function setHotelFoods(array $hotelFoods): static
    {
        $this->hotelFoods = $hotelFoods;

        return $this;
    }

    public function getHotelStars(): ?int
    {
        return $this->hotelStars;
    }

    public function setHotelStars(?int $hotelStars): static
    {
        $this->hotelStars = $hotelStars;

        return $this;
    }

    public function getHotelRate(): ?float
    {
        return $this->hotelRate;
    }

    public function setHotelRate(?float $hotelRate): static
    {
        $this->hotelRate = $hotelRate;

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
     * @param class-string[] $todo
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

    #[Groups(['last_minute', 'last_minute_collection'])]
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
     * @return Collection<int, Trip>
     */
    public function getTrips(): Collection
    {
        return $this->trips;
    }

    public function addTrip(Trip $trip): static
    {
        if (!$this->trips->contains($trip)) {
            $this->trips->add($trip);
            $trip->setLastMinute($this);
        }

        return $this;
    }

    public function getServiceTodo(): ?string
    {
        return array_shift($this->todo);
    }

    /**
     * @return array<string, int>
     */
    #[Groups(['last_minute', 'last_minute_collection'])]
    public function getCountServices(): array
    {
        return array_count_values([
            ...$this->trips->map(fn (SourceInterface $source): string => $source->getSource())->toArray(),
        ]);
    }
}
