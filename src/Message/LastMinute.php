<?php

namespace App\Message;

final readonly class LastMinute
{
    public function __construct(private int $lastMinuteId)
    {
    }

    public function getLastMinuteId(): int
    {
        return $this->lastMinuteId;
    }
}
