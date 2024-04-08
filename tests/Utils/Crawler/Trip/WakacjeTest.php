<?php

namespace App\Tests\Utils\Crawler\Trip;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\Common\Money;
use App\Utils\Crawler\Trip\Model\Trip;
use App\Utils\Crawler\Trip\Wakacje;
use App\Utils\Enum\Currency;
use App\Utils\Enum\Food;

class WakacjeTest extends ContainerKernelTestCase
{
    public function testGetSource(): void
    {
        $this->assertSame(Wakacje::class, $this->getTripWakacje()->getSource());
    }

    public function testGetTrips(): void
    {
        $result = $this->getTripWakacje()->getTrips(
            'zakynthos',
            new \DateTime('01-01-2000'),
            new \DateTime('06-01-2000'),
            5,
            7
        );

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Trip::class, $result[0]);
        $this->assertSame('MarianaMariana', $result[0]->getTitle());
        $this->assertSame('https://www.wakacje.pl/oferty/grecja/zakynthos/laganas/mariana-808531.html?od-2024-09-29,7-dni,wlasne,z-warszawy', $result[0]->getUrl());
        $this->assertSame(Food::WITHOUT_FOOD, $result[0]->getFood());
        $this->assertSame(3, $result[0]->getStars());
        $this->assertSame(0.0, $result[0]->getRate());
        $this->assertIsString($result[0]->getImage());
        $this->assertInstanceOf(Money::class, $result[0]->getMoney());
        $this->assertSame(1399.0, $result[0]->getMoney()->getPrice());
        $this->assertSame(true, $result[0]->getMoney()->isPriceForOnePerson());
        $this->assertSame(Currency::PLN, $result[0]->getMoney()->getCurrency());
    }
}
