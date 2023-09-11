<?php

namespace App\Entity;

use App\Repository\TripArticleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripArticleRepository::class)]
class TripArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\ManyToOne(inversedBy: 'tripArticles')]
    private TripPage $page;

    /** @var string[] */
    #[ORM\Column]
    private array $descriptions = [];

    /** @var string[] */
    #[ORM\Column]
    private array $images = [];

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

    public function getPage(): TripPage
    {
        return $this->page;
    }

    public function setPage(TripPage $page): static
    {
        $this->page = $page;

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
     *
     * @return $this
     */
    public function setDescriptions(array $descriptions): static
    {
        $this->descriptions = $descriptions;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param string[] $images
     *
     * @return $this
     */
    public function setImages(array $images): static
    {
        $this->images = $images;

        return $this;
    }
}
