<?php

namespace App\Entity;

use App\Repository\TripPageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripPageRepository::class)]
class TripPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $url;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $map = null;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\TripArticle>|\App\Entity\TripArticle[]
     */
    #[ORM\OneToMany(mappedBy: 'page', targetEntity: TripArticle::class)]
    private Collection $tripArticles;

    #[ORM\Column(length: 255)]
    private string $source;

    public function __construct()
    {
        $this->tripArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
}