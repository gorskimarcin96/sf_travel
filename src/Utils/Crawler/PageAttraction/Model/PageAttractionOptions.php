<?php

namespace App\Utils\Crawler\PageAttraction\Model;

final readonly class PageAttractionOptions
{
    /**
     * @param string[] $titleSelectors
     * @param string[] $imageSelectors
     * @param string[] $textSelectors
     */
    public function __construct(
        private string $url,
        private string $mainPageSelector,
        private string $mainContentSelector,
        private array $titleSelectors = ['h1', 'h2', 'h3', 'h4', 'h5'],
        private array $imageSelectors = ['p', 'div'],
        private array $textSelectors = ['p', 'div']
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMainPageSelector(): string
    {
        return $this->mainPageSelector;
    }

    public function getMainContentSelector(): string
    {
        return $this->mainContentSelector;
    }

    /**
     * @return string[]
     */
    public function getTitleSelectors(): array
    {
        return $this->titleSelectors;
    }

    /**
     * @return string[]
     */
    public function getImageSelectors(): array
    {
        return $this->imageSelectors;
    }

    /**
     * @return string[]
     */
    public function getTextSelectors(): array
    {
        return $this->textSelectors;
    }
}
