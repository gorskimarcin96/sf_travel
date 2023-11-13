<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\HotelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(operations: [new GetCollection()], normalizationContext: ['groups' => ['hotels']])]
#[ApiFilter(SearchFilter::class, properties: ['search' => 'exact', 'source' => 'exact'])]
#[ORM\Entity(repositoryClass: HotelRepository::class)]
class Hotel implements SourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('hotels')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('hotels')]
    private string $title;

    #[ORM\Column(length: 1000)]
    #[Groups('hotels')]
    private string $url;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::TEXT)]
    #[Groups('hotels')]
    private string $image;

    #[ORM\Column(length: 255)]
    #[Groups('hotels')]
    private string $address;

    /** @var string[] */
    #[ORM\Column()]
    #[Groups('hotels')]
    private array $descriptions = [];

    #[ORM\Column(nullable: true)]
    #[Groups('hotels')]
    private ?float $rate = null;

    #[ORM\OneToOne(cascade: ['all'])]
    #[Orm\JoinColumn(onDelete: 'CASCADE')]
    #[Groups('hotels')]
    private Money $money;

    #[ORM\Column(length: 255)]
    #[Groups('hotels')]
    private string $source;

    #[ORM\ManyToOne(inversedBy: 'hotel')]
    private ?Search $search = null;

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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    /**
     * @param string[] $descriptions
     */
    public function setDescriptions(array $descriptions): Hotel
    {
        $this->descriptions = $descriptions;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(?float $rate): static
    {
        $this->rate = $rate;

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
