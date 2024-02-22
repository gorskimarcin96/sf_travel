<?php

namespace App\Tests\Mocks;

final class WebDriverKeyboard implements \Facebook\WebDriver\WebDriverKeyboard
{
    #[\Override]
    public function sendKeys($keys): WebDriverKeyboard
    {
        return $this;
    }

    #[\Override]
    public function pressKey($key): WebDriverKeyboard
    {
        return $this;
    }

    #[\Override]
    public function releaseKey($key): WebDriverKeyboard
    {
        return $this;
    }
}
