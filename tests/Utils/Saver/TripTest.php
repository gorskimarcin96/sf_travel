<?php

namespace App\Tests\Utils\Saver;

use App\Entity\Search;
use App\Entity\Trip;
use App\Factory\Money;
use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\Trip\Model\Trip as TripModel;
use App\Utils\Crawler\Trip\TripInterface;
use App\Utils\Enum\Currency;
use App\Utils\Enum\Food;

class TripTest extends ContainerKernelTestCase
{
    public function testSave(): void
    {
        $this->getEntityManager(true);

        $tripService = $this->createMock(TripInterface::class);
        $tripService
            ->expects(self::once())
            ->method('getTrips')
            ->willReturn([
                new TripModel(
                    'First trip',
                    '#1',
                    Food::ALL_INCLUSIVE,
                    4,
                    7.5,
                    '#',
                    new \DateTimeImmutable('01-01-2000'),
                    new \DateTimeImmutable('07-01-2000'),
                    Money::create(1999.99)
                ),
                new TripModel(
                    'Second trip',
                    '#2',
                    Food::BREAKFAST_LAUNCH_AND_DINNER,
                    3,
                    8.5,
                    '#',
                    new \DateTimeImmutable('01-01-2000'),
                    new \DateTimeImmutable('05-01-2000'),
                    Money::create(499.99, Currency::EUR)
                ),
                new TripModel(
                    'Third trip',
                    '#3',
                    Food::ALL_INCLUSIVE,
                    4,
                    4.9,
                    '#',
                    new \DateTimeImmutable('02-01-2000'),
                    new \DateTimeImmutable('07-01-2000'),
                    Money::create(1999.99)
                ),
            ]);
        $tripService
            ->method('getSource')
            ->willReturn(__CLASS__);

        $this->getSaverTrip()->save(
            $tripService,
            'Zakynthos',
            new \DateTimeImmutable('01-01-2020'),
            new \DateTimeImmutable('07-01-2020'),
            5,
            7,
            [Food::ALL_INCLUSIVE, Food::BREAKFAST_LAUNCH_AND_DINNER],
            4,
            4,
            2,
            0,
            new Search()
        );

        /** @var Trip[] $tripEntities */
        $tripEntities = $this->getEntityManager()->getFlushEntities();

        $this->assertCount(3, $tripEntities);
        $this->assertInstanceOf(Trip::class, $tripEntities[0]);
        $this->assertSame('First trip', $tripEntities[0]->getTitle());
        $this->assertSame('#1', $tripEntities[0]->getUrl());
        $this->assertSame(Food::ALL_INCLUSIVE, $tripEntities[0]->getFood());
        $this->assertSame(4, $tripEntities[0]->getStars());
        $this->assertSame(7.5, $tripEntities[0]->getRate());
        $this->assertSame('#', $tripEntities[0]->getImage());
        $this->assertSame(946684800, $tripEntities[0]->getFrom()->getTimestamp());
        $this->assertSame(947203200, $tripEntities[0]->getTo()->getTimestamp());
        $this->assertInstanceOf(\App\Entity\Money::class, $tripEntities[0]->getMoney());
        $this->assertSame(1999.99, $tripEntities[0]->getMoney()->getPrice());
        $this->assertSame(Currency::PLN, $tripEntities[0]->getMoney()->getCurrency());
        $this->assertSame(__CLASS__, $tripEntities[0]->getSource());
    }
}
