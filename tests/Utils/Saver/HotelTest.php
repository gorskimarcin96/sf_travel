<?php

namespace App\Tests\Utils\Saver;

use App\Entity\Hotel;
use App\Entity\Search;
use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\Common\Money;
use App\Utils\Crawler\Hotel\HotelInterface;
use App\Utils\Crawler\Hotel\Model\Hotel as HotelModel;
use App\Utils\Enum\Currency;
use App\Utils\Enum\Food;

class HotelTest extends ContainerKernelTestCase
{
    public function testSave(): void
    {
        $this->getEntityManager(true);

        $hotelService = $this->createMock(HotelInterface::class);
        $hotelService
            ->expects(self::once())
            ->method('getHotels')
            ->willReturn([
                new HotelModel(
                    'First hotel',
                    '#1',
                    Food::ALL_INCLUSIVE,
                    2,
                    2.2,
                    '#',
                    'First address',
                    ['It\'s nice!'],
                    new \DateTimeImmutable('01-01-2020'),
                    new \DateTimeImmutable('07-01-2020'),
                    new Money(1999.99),
                ),
                new HotelModel(
                    'Second hotel',
                    '#2',
                    Food::BREAKFAST_LAUNCH_AND_DINNER,
                    4,
                    9.0,
                    '#',
                    'Second address',
                    [],
                    new \DateTimeImmutable('03-01-2020'),
                    new \DateTimeImmutable('07-01-2020'),
                    new Money(999.99),
                ),
            ]);
        $hotelService
            ->method('getSource')
            ->willReturn(__CLASS__);

        $this->getSaverHotel()->save(
            $hotelService,
            'Zakynthos',
            new \DateTimeImmutable('01-01-2020'),
            new \DateTimeImmutable('07-01-2020'),
            5,
            7,
            [Food::ALL_INCLUSIVE, Food::BREAKFAST_LAUNCH_AND_DINNER],
            2,
            2,
            2,
            0,
            new Search()
        );

        /** @var Hotel[] $hotelEntities */
        $hotelEntities = $this->getEntityManager()->getFlushEntities();

        $this->assertCount(2, $hotelEntities);
        $this->assertInstanceOf(Hotel::class, $hotelEntities[0]);
        $this->assertSame('First hotel', $hotelEntities[0]->getTitle());
        $this->assertSame('#1', $hotelEntities[0]->getUrl());
        $this->assertSame(Food::ALL_INCLUSIVE, $hotelEntities[0]->getFood());
        $this->assertSame(2, $hotelEntities[0]->getStars());
        $this->assertSame(2.2, $hotelEntities[0]->getRate());
        $this->assertSame('First address', $hotelEntities[0]->getAddress());
        $this->assertSame('#', $hotelEntities[0]->getImage());
        $this->assertSame(['It\'s nice!'], $hotelEntities[0]->getDescriptions());
        $this->assertSame(1577836800, $hotelEntities[0]->getFrom()->getTimestamp());
        $this->assertSame(1578355200, $hotelEntities[0]->getTo()->getTimestamp());
        $this->assertSame(1999.99, $hotelEntities[0]->getPrice());
        $this->assertSame(Currency::PLN, $hotelEntities[0]->getCurrency());
        $this->assertSame(__CLASS__, $hotelEntities[0]->getSource());
    }
}
