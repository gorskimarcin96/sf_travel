<?php

namespace App\Tests\Utils\Saver;

use App\Entity\OptionalTrip;
use App\Entity\Search;
use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\Common\Money;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip as OptionalTripModel;
use App\Utils\Crawler\OptionalTrip\OptionalTripInterface;
use App\Utils\Enum\Currency;

class OptionalTripTest extends ContainerKernelTestCase
{
    public function testSave(): void
    {
        $this->getEntityManager(true);

        $optionalTripService = $this->createMock(OptionalTripInterface::class);
        $optionalTripService
            ->expects(self::once())
            ->method('getOptionalTrips')
            ->willReturn([
                new OptionalTripModel(
                    'First optional trip',
                    'Description',
                    '#1',
                    '#',
                    new Money(29.99, true, Currency::EUR)
                ),
                new OptionalTripModel(
                    'Second optional trip',
                    'Description',
                    '#2',
                    '#',
                    new Money(19.99, true, Currency::EUR)
                ),
                new OptionalTripModel(
                    'Third optional trip',
                    'Description',
                    '#3',
                    '#',
                    new Money(99.99, true, Currency::PLN)
                ),
            ]);
        $optionalTripService
            ->method('getSource')
            ->willReturn(__CLASS__);

        $this->getSaverOptionalTrip()->save(
            $optionalTripService,
            'Zakynthos',
            'Grecja',
            new Search()
        );

        /** @var OptionalTrip[] $optionalTripEntities */
        $optionalTripEntities = $this->getEntityManager()->getFlushEntities();

        $this->assertCount(3, $optionalTripEntities);
        $this->assertInstanceOf(OptionalTrip::class, $optionalTripEntities[0]);
        $this->assertSame('First optional trip', $optionalTripEntities[0]->getTitle());
        $this->assertSame(['Description'], $optionalTripEntities[0]->getDescription());
        $this->assertSame('#', $optionalTripEntities[0]->getImage());
        $this->assertSame('#1', $optionalTripEntities[0]->getUrl());
        $this->assertSame(29.99, $optionalTripEntities[0]->getPrice());
        $this->assertSame(true, $optionalTripEntities[0]->isPriceForOnePerson());
        $this->assertSame(Currency::EUR, $optionalTripEntities[0]->getCurrency());
        $this->assertSame(__CLASS__, $optionalTripEntities[0]->getSource());
    }
}
