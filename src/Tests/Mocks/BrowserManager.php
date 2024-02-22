<?php

namespace App\Tests\Mocks;

use Facebook\WebDriver\WebDriver;
use Symfony\Component\Panther\ProcessManager\BrowserManagerInterface;

final readonly class BrowserManager implements BrowserManagerInterface
{
    public function __construct(private \App\Tests\Mocks\WebDriver $webDriver)
    {
    }

    #[\Override]
    public function start(): WebDriver
    {
        return $this->webDriver;
    }

    #[\Override]
    public function quit(): void
    {
        // TODO: Implement quit() method.
    }
}
