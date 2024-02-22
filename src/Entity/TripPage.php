<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\TripPageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(operations: [new GetCollection()], normalizationContext: ['groups' => ['trip-page']])]
#[ApiFilter(SearchFilter::class, properties: ['search' => 'exact', 'source' => 'exact'])]
#[ORM\Entity(repositoryClass: TripPageRepository::class)]
class TripPage implements SourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue('SEQUENCE')]
    #[ORM\Column]
    #[Groups('trip-page')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('trip-page')]
    private string $url;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('trip-page')]
    private ?string $map = null;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\TripArticle>|\App\Entity\TripArticle[]
     */
    #[ORM\OneToMany(mappedBy: 'tripPage', targetEntity: TripArticle::class, cascade: ['persist'])]
    #[Groups('trip-page')]
    private Collection $tripArticles;

    #[ORM\Column(length: 255)]
    #[Groups('trip-page')]
    private string $source;

    #[ORM\ManyToOne(inversedBy: 'tripPages')]
    private ?Search $search = null;

    public function __construct()
    {
        $this->tripArticles = new ArrayCollection();
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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getMap(): ?string
    {
        return $this->map;
    }

    public function setMap(?string $map): static
    {
        $this->map = $map;

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

    /** @return Collection<int, TripArticle> */
    public function getTripArticles(): Collection
    {
        return $this->tripArticles;
    }

    public function addTripArticle(TripArticle $tripArticle): static
    {
        if (!$this->tripArticles->contains($tripArticle)) {
            $this->tripArticles->add($tripArticle);
            $tripArticle->setPage($this);
        }

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
