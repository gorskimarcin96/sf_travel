<?php

namespace App\Tests\Utils\Saver;

use App\Entity\Flight;
use App\Entity\Search;
use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\Common\Money;
use App\Utils\Crawler\Flight\FlightInterface;
use App\Utils\Crawler\Flight\Model\Flight as FlightModel;
use App\Utils\Enum\Currency;

class FlightTest extends ContainerKernelTestCase
{
    public function testSave(): void
    {
        $this->getEntityManager(true);

        $flightService = $this->createMock(FlightInterface::class);
        $flightService
            ->expects(self::once())
            ->method('getFlights')
            ->willReturn([
                new FlightModel(
                    'WAW',
                    new \DateTimeImmutable('00:00 01-01-2020'),
                    new \DateTimeImmutable('02:00 01-01-2020'),
                    2,
                    'ZTH',
                    new \DateTimeImmutable('00:00 07-01-2020'),
                    new \DateTimeImmutable('02:00 07-01-2020'),
                    0,
                    new Money(500.00),
                    '#'
                ),
                new FlightModel(
                    'WAW',
                    new \DateTimeImmutable('10:00 01-01-2020'),
                    new \DateTimeImmutable('12:00 01-01-2020'),
                    0,
                    'ZTH',
                    new \DateTimeImmutable('10:00 07-01-2020'),
                    new \DateTimeImmutable('12:00 17-01-2020'),
                    1,
                    new Money(499.99),
                    '#'
                ),
            ]);
        $flightService
            ->method('getSource')
            ->willReturn(__CLASS__);

        $this->getSaverFlight()->save(
            $flightService,
            'WAW',
            'ZTH',
            new \DateTimeImmutable('01-01-2020'),
            new \DateTimeImmutable('07-01-2020'),
            2,
            0,
            new Search()
        );

        /** @var \App\Entity\Flight[] $flushEntities */
        $flushEntities = $this->getEntityManager()->getFlushEntities();

        $this->assertCount(2, $flushEntities);
        $this->assertInstanceOf(Flight::class, $flushEntities[0]);
        $this->assertSame('WAW', $flushEntities[0]->getFromAirport());
        $this->assertSame(1577836800, $flushEntities[0]->getFromStart()->getTimestamp());
        $this->assertSame(1577844000, $flushEntities[0]->getFromEnd()->getTimestamp());
        $this->assertSame(2, $flushEntities[0]->getFromStops());
        $this->assertSame('ZTH', $flushEntities[0]->getToAirport());
        $this->assertSame(1578355200, $flushEntities[0]->getToStart()->getTimestamp());
        $this->assertSame(1578362400, $flushEntities[0]->getToEnd()->getTimestamp());
        $this->assertSame(0, $flushEntities[0]->getToStops());
        $this->assertSame(500.00, $flushEntities[0]->getPrice());
        $this->assertSame(Currency::PLN, $flushEntities[0]->getCurrency());
        $this->assertSame(__CLASS__, $flushEntities[0]->getSource());
    }
}
