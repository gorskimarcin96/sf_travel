<?php

namespace App\Tests\Utils\Saver;

use App\Entity\Money;
use App\Entity\Search;
use App\Tests\Mocks\OptionalTrip as Mock;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip as Model;
use App\Utils\Saver\OptionalTrip as Saver;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OptionalTripTest extends KernelTestCase
{
    private Saver $service;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Saver $service */
        $service = self::getContainer()->get(Saver::class);
        $this->service = $service;
    }

    public function testSave(): void
    {
        $result = $this->service->save(
            new Mock([new Model('title', ['description'], 'https://example.org', 'https://example.jpg', new Money())]),
            'test',
            'test',
            new Search()
        );

        $this->assertInstanceOf(Search::class, $result);
        $optionalTrip = $result->getOptionalTrips()->first() ?: throw new EntityNotFoundException();
        $this->assertSame('title', $optionalTrip->getTitle());
        $this->assertSame(['description'], $optionalTrip->getDescription());
        $this->assertSame('https://example.org', $optionalTrip->getUrl());
        $this->assertSame('https://example.jpg', $optionalTrip->getImg());
        $this->assertInstanceOf(Money::class, $optionalTrip->getMoney());
    }
}
