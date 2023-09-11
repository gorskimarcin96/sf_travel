<?php

namespace App\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client;

class SeleniumClientTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertInstanceOf(Client::class, Client::createSeleniumClient('localhost'));
    }
}
