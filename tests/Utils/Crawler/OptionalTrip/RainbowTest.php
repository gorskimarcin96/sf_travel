<?php

namespace App\Tests\Utils\Crawler\OptionalTrip;

use App\Entity\Money;
use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip;
use App\Utils\Crawler\OptionalTrip\Rainbow;
use App\Utils\Enum\Currency;

class RainbowTest extends ContainerKernelTestCase
{
    public function testGetSource(): void
    {
        $this->assertSame(Rainbow::class, $this->getOptionalTripRainbow()->getSource());
    }

    public function testGetOptionalTripsByUrl(): void
    {
        $result = $this->getOptionalTripRainbow()->getOptionalTrips('zakynthos', 'grecja');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(OptionalTrip::class, $result[0]);
        $this->assertSame('Magiczne Zante', $result[0]->getTitle());
        $this->assertSame([
            '• Błękitne Groty - Blue Caves',
            '• Tłocznia oliwy z oliwek',
            '• Stragany z rękodziełem',
        ], $result[0]->getDescription());
        $this->assertSame('https://r.pl/wycieczki-fakultatywne/kierunki/grecja/zakynthos/magiczne-zante-468', $result[0]->getUrl());
        $this->assertIsString($result[0]->getImage());
        $this->assertInstanceOf(Money::class, $result[0]->getMoney());
        $this->assertSame(209.0, $result[0]->getMoney()->getPrice());
        $this->assertSame(Currency::PLN, $result[0]->getMoney()->getCurrency());
    }

    public function testGetOptionalTripsBySearchTitle(): void
    {
        $result = $this->getOptionalTripRainbow()->getOptionalTrips('kreta', 'grecja');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(OptionalTrip::class, $result[0]);
        $this->assertSame('Knossos i Heraklion z wizytą u Minotaura', $result[0]->getTitle());
        $this->assertSame(['Pałac minojski w Knossos'], $result[0]->getDescription());
        $this->assertSame('https://r.pl/wycieczki-fakultatywne/kierunki/grecja/kreta-chania/knossos-i-heraklion-z-wizyta-u-minotaura-627', $result[0]->getUrl());
        $this->assertIsString($result[0]->getImage());
        $this->assertInstanceOf(Money::class, $result[0]->getMoney());
        $this->assertSame(279.0, $result[0]->getMoney()->getPrice());
        $this->assertSame(Currency::PLN, $result[0]->getMoney()->getCurrency());
    }
}
