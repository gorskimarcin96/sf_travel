<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\OptionalTripRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(operations: [new GetCollection()], normalizationContext: ['groups' => ['optional-trips']])]
#[ApiFilter(SearchFilter::class, properties: ['search' => 'exact', 'source' => 'exact'])]
#[ORM\Entity(repositoryClass: OptionalTripRepository::class)]
class OptionalTrip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('optional-trips')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('optional-trips')]
    private string $title;

    /**
     * @var string[]
     */
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::JSON)]
    #[Groups('optional-trips')]
    private array $description = [];

    #[ORM\Column(length: 255)]
    #[Groups('optional-trips')]
    private string $url;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::TEXT)]
    #[Groups('optional-trips')]
    private string $img;

    #[ORM\Column(length: 255)]
    #[Groups('optional-trips')]
    private string $source;

    #[ORM\OneToOne(cascade: ['all'])]
    #[Orm\JoinColumn(onDelete: 'CASCADE')]
    #[Groups('optional-trips')]
    private Money $money;

    #[ORM\ManyToOne(inversedBy: 'optionalTrips')]
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
