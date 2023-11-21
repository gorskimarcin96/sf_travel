<?php

namespace App\Utils\Crawler\FlyCodes\Model;

final readonly class FlyCode
{
    public function __construct(private string $code, private string $city, private string $nation)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getNation(): string
    {
        return $this->nation;
    }
}
