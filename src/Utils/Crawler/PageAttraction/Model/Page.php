<?php

namespace App\Utils\Crawler\PageAttraction\Model;

class Page
{
    /** @param \App\Utils\Crawler\PageAttraction\Model\Article[] $articles */
    public function __construct(private readonly string $url, private array $articles = [], private ?string $map = null)
    {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /** @return Article[] */
    public function getArticles(): array
    {
        return $this->articles;
    }

    public function getMap(): ?string
    {
        return $this->map;
    }

    public function setMap(?string $map): void
    {
        $this->map = $map;
    }

    public function addArticle(Article $article): void
    {
        $this->articles[] = $article;
    }

    public function lastArticle(): ?Article
    {
        return $this->articles ? $this->articles[count($this->articles) - 1] : null;
    }
}
