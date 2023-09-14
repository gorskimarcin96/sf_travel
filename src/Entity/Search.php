<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\HttpOperation;
use App\Controller\SearchController;
use App\Repository\SearchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(operations: [
    new Get(uriTemplate: '/{id}'),
    new HttpOperation(
        method: 'POST',
        uriTemplate: '',
        status: Response::HTTP_CREATED,
        controller: SearchController::class,
        input: \App\ApiResource\Input\Search::class
    ),
], routePrefix: '/search', normalizationContext: ['groups' => ['search']])]
#[ORM\Entity(repositoryClass: SearchRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Search
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('search')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('search')]
    private string $nation;

    #[ORM\Column(length: 255)]
    #[Groups('search')]
    private string $place;

    /**
     * @var string[]
     */
    #[ORM\Column()]
    #[Groups('search')]
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
    #[Groups('search')]
    private array $errors = [];

    #[ORM\Column]
    #[Groups('search')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups('search')]
    private \DateTimeImmutable $updatedAt;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\OptionalTrip>|\App\Entity\OptionalTrip[]
     */
    #[ORM\OneToMany(mappedBy: 'search', targetEntity: OptionalTrip::class)]
    private Collection $optionalTrips;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\TripPage>|\App\Entity\TripPage[]
     */
    #[ORM\OneToMany(mappedBy: 'search', targetEntity: TripPage::class)]
    private Collection $tripPages;

    public function __construct()
    {
        $this->optionalTrips = new ArrayCollection();
        $this->tripPages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNation(): string
    {
        return $this->nation;
    }

    public function setNation(string $nation): static
    {
        $this->nation = $nation;

        return $this;
    }

    public function getPlace(): string
    {
        return $this->place;
    }

    public function setPlace(string $place): static
    {
        $this->place = $place;

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

    #[Groups('search')]
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

    public function getServiceTodo(): ?string
    {
        return array_pop($this->todo);
    }
}