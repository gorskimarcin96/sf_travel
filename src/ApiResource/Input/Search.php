<?php

namespace App\ApiResource\Input;

final readonly class Search
{
    public function __construct(
        private string $nation,
        private string $place,
        private bool $force = false
    ) {
    }

    public function getNation(): string
    {
        return $this->nation;
    }

    public function getPlace(): string
    {
        return $this->place;
    }

    public function isForce(): bool
    {
        return $this->force;
    }
}
