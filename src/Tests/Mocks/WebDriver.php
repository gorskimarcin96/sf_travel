<?php

namespace App\Tests\Mocks;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverHasInputDevices;
use Facebook\WebDriver\WebDriverKeyboard;
use Facebook\WebDriver\WebDriverMouse;
use Facebook\WebDriver\WebDriverNavigationInterface;
use Facebook\WebDriver\WebDriverOptions;
use Facebook\WebDriver\WebDriverTargetLocator;
use Facebook\WebDriver\WebDriverWait;
use Symfony\Component\DomCrawler\Crawler;

final class WebDriver implements \Facebook\WebDriver\WebDriver, WebDriverHasInputDevices
{
    private string $response;
    private string $currentURL;

    /**
     * @param array<string, string> $responses
     */
    public function __construct(private readonly array $responses = [])
    {
        if ([] !== $responses) {
            $this->get(array_key_first($responses));
        }
    }

    #[\Override]
    public function close(): WebDriver
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function get($url): WebDriver
    {
        if ('' === $url || '0' === $url) {
            throw new \LogicException('Url is not valid.');
        }

        $this->currentURL = $url;
        $this->response = $this->responses[$url]
            ?? throw new \LogicException(sprintf('Content is not exists for %s url.', $url));

        return $this;
    }

    #[\Override]
    public function getCurrentURL(): string
    {
        return $this->currentURL;
    }

    #[\Override]
    public function getPageSource(): string
    {
        return $this->response;
    }

    #[\Override]
    public function getTitle(): string
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getWindowHandle(): string
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getWindowHandles(): array
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function quit()
    {
    }

    #[\Override]
    public function takeScreenshot($save_as = null): string
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function wait($timeout_in_second = 30, $interval_in_millisecond = 250): WebDriverWait
    {
        return new WebDriverWait($this, $timeout_in_second, $interval_in_millisecond);
    }

    #[\Override]
    public function manage(): WebDriverOptions
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function navigate(): WebDriverNavigationInterface
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function switchTo(): WebDriverTargetLocator
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function execute($name, $params): mixed
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function findElement(WebDriverBy $locator): WebDriverElement
    {
        return new WebDriverElement((new Crawler($this->response))->filter($locator->getValue())->first());
    }

    /**
     * @return WebDriverElement[]
     */
    #[\Override]
    public function findElements(WebDriverBy $locator): array
    {
        return (new Crawler($this->response))
            ->filter($locator->getValue())
            ->each(fn (Crawler $node): WebDriverElement => new WebDriverElement($node));
    }

    #[\Override]
    public function getKeyboard(): WebDriverKeyboard
    {
        return new \App\Tests\Mocks\WebDriverKeyboard();
    }

    #[\Override]
    public function getMouse(): WebDriverMouse
    {
        throw new \LogicException('Method is not implemented.');
    }
}
