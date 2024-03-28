<?php

namespace App\Tests\Utils\Crawler\Model;

use App\Utils\Crawler\Common\Money;
use App\Utils\Crawler\Hotel\Model\Hotel;
use App\Utils\Crawler\Model\URLTrait;
use App\Utils\Enum\Food;
use PHPUnit\Framework\TestCase;

class URLTraitTest extends TestCase
{
    use URLTrait;

    public function testUniqueByUrl(): void
    {
        $models = [
            new Hotel('title 1', 'https://example.1.com', Food::ALL_INCLUSIVE, 0, null, '', '', [], new \DateTime(), new \DateTime(), new Money(0)),
            new Hotel('title 2', 'https://example.2.com', Food::ALL_INCLUSIVE, 0, null, '', '', [], new \DateTime(), new \DateTime(), new Money(0)),
            new Hotel('title 3', 'https://example.3.com', Food::ALL_INCLUSIVE, 0, null, '', '', [], new \DateTime(), new \DateTime(), new Money(0)),
            new Hotel('title 4', 'https://example.4.com', Food::ALL_INCLUSIVE, 0, null, '', '', [], new \DateTime(), new \DateTime(), new Money(0)),
            new Hotel('title 5', 'https://example.5.com', Food::ALL_INCLUSIVE, 0, null, '', '', [], new \DateTime(), new \DateTime(), new Money(0)),
        ];

        /** @var Hotel[] $models */
        $models = $this->uniqueByUrl($models);
        $this->assertSame('title 1', $models[0]->getTitle());
        $this->assertSame('https://example.1.com', $models[0]->getUrl());
        $this->assertSame('title 2', $models[1]->getTitle());
        $this->assertSame('https://example.2.com', $models[1]->getUrl());
        $this->assertSame('title 3', $models[2]->getTitle());
        $this->assertSame('https://example.3.com', $models[2]->getUrl());
        $this->assertSame('title 4', $models[3]->getTitle());
        $this->assertSame('https://example.4.com', $models[3]->getUrl());
    }
}
