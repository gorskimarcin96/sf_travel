<?php

namespace App\Factory;

use Symfony\Component\Panther\Client;

class SeleniumClient
{
    public static function create(string $seleniumUrl): Client
    {
        return Client::createSeleniumClient($seleniumUrl);
    }
}
