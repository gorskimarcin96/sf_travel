<?php

namespace App\Message;

final readonly class Search
{
    public function __construct(private int $searchId)
    {
    }

    public function getSearchId(): int
    {
        return $this->searchId;
    }
}
