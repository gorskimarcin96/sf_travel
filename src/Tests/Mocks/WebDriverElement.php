<?php

namespace App\Tests\Mocks;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverPoint;
use Symfony\Component\DomCrawler\Crawler;

final readonly class WebDriverElement implements \Facebook\WebDriver\WebDriverElement
{
    public function __construct(private Crawler $crawler)
    {
    }

    #[\Override]
    public function clear(): WebDriverElement
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function click(): WebDriverElement
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getAttribute($attribute_name): ?string
    {
        return $this->crawler->attr($attribute_name);
    }

    #[\Override]
    public function getCSSValue($css_property_name): string
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getLocation(): WebDriverPoint
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getLocationOnScreenOnceScrolledIntoView(): WebDriverPoint
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getSize(): WebDriverDimension
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getTagName(): string
    {
        return $this->crawler->first()->nodeName();
    }

    #[\Override]
    public function getText(): string
    {
        return $this->crawler->first()->text();
    }

    #[\Override]
    public function isDisplayed(): bool
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function isEnabled(): bool
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function isSelected(): bool
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function sendKeys($value): WebDriverElement
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function submit(): WebDriverElement
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getID(): string
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function findElement(WebDriverBy $locator): \Facebook\WebDriver\WebDriverElement
    {
        return new WebDriverElement($this->crawler->filter($locator->getValue())->first());
    }

    /**
     * @return \Facebook\WebDriver\WebDriverElement[]
     */
    #[\Override]
    public function findElements(WebDriverBy $locator): array
    {
        return $this->crawler
            ->filter($locator->getValue())
            ->each(fn (Crawler $node): WebDriverElement => new WebDriverElement($node));
    }
}
