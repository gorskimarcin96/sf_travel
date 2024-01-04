<?php

namespace App\Utils\Api\Translation\Model;

final readonly class Translation
{
    public function __construct(private string $text)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }
}
