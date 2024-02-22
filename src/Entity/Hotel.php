<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\HotelRepository;
use App\Utils\Enum\Food;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/** @codeCoverageIgnore */
#[ApiResource(operations: [new GetCollection()], normalizationContext: ['groups' => ['hotels']])]
#[ApiFilter(SearchFilter::class, properties: ['search' => 'exact', 'source' => 'exact'])]
#[ORM\Entity(repositoryClass: HotelRepository::class)]
class Hotel implements SourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue('SEQUENCE')]
    #[ORM\Column]
    #[Groups('hotels')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('hotels')]
    private string $title;

    #[ORM\Column(length: 1000)]
    #[Groups('hotels')]
    private string $url;

    #[ORM\Column()]
    #[Groups('hotels')]
    private int $stars;

    #[ORM\Column()]
    #[Groups('hotels')]
    private ?float $rate = null;

    #[ORM\Column()]
    #[Groups('hotels')]
    private Food $food;

    #[ORM\Column(name: 'from_at', type: \Doctrine\DBAL\Types\Types::DATE_IMMUTABLE, length: 255)]
    #[Groups('hotels')]
    private \DateTimeImmutable $from;

    #[ORM\Column(name: 'to_at', type: \Doctrine\DBAL\Types\Types::DATE_IMMUTABLE, length: 255)]
    #[Groups('hotels')]
    private \DateTimeImmutable $to;

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

    #[ORM\OneToOne(cascade: ['all'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Groups('hotels')]
    private Money $money;

    #[ORM\Column(length: 255)]
    #[Groups('hotels')]
    private string $source;

    #[ORM\ManyToOne(inversedBy: 'hotels')]
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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getStars(): int
    {
        return $this->stars;
    }

    public function setStars(int $stars): static
    {
        $this->stars = $stars;

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

    public function getFood(): Food
    {
        return $this->food;
    }

    public function setFood(Food $food): static
    {
        $this->food = $food;

        return $this;
    }

    public function getFrom(): \DateTimeImmutable
    {
        return $this->from;
    }

    public function setFrom(\DateTimeImmutable $from): static
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): \DateTimeImmutable
    {
        return $this->to;
    }

    public function setTo(\DateTimeImmutable $to): static
    {
        $this->to = $to;

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
    public function setDescriptions(array $descriptions): static
    {
        $this->descriptions = $descriptions;

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
