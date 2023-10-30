<?php

namespace App\ApiResource\Input;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Search
{
    public function __construct(
        #[Assert\NotBlank]
        private string $nation,
        #[Assert\NotBlank]
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
