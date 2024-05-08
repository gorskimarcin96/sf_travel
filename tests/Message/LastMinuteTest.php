<?php

namespace App\Tests\Message;

use App\Message\LastMinute;
use PHPUnit\Framework\TestCase;

class LastMinuteTest extends TestCase
{
    public function testGetLastMinuteId(): void
    {
        $lastMinute = new LastMinute(7);

        $this->assertSame(7, $lastMinute->getLastMinuteId());
    }
}
