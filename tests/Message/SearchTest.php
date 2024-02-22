<?php

namespace App\Tests\Message;

use App\Message\Search;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    public function testGetSearchId(): void
    {
        $search = new Search(5);

        $this->assertSame(5, $search->getSearchId());
    }
}
