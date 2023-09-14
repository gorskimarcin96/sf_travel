<?php

namespace App\Utils\Crawler\PageAttraction\Model;

final class Article
{
    /**
     * @param string[] $descriptions
     * @param string[] $images
     */
    public function __construct(
        private readonly string $title,
        private array $descriptions = [],
        private array $images = []
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /** @return string[] */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    /** @return string[] */
    public function getImages(): array
    {
        return $this->images;
    }

    public function addDescription(string $description): void
    {
        $this->descriptions[] = $description;
    }

    public function addImage(string $url): void
    {
        $this->images[] = $url;
    }
}
