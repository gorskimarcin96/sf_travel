<?php

namespace App\Tests\Utils\Crawler\OptionalTrip;

use App\Entity\Money;
use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\OptionalTrip\Itaka;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip;
use App\Utils\Enum\Currency;
use App\Utils\Faker\Invoker;

class ItakaTest extends ContainerKernelTestCase
{
    use Invoker;

    public function testGetSource(): void
    {
        $this->assertSame(Itaka::class, $this->getOptionalTripItaka()->getSource());
    }

    public function testGetOptionalTripsByUrl(): void
    {
        $result = $this->getOptionalTripItaka()->getOptionalTrips('zakynthos', 'grecja');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(OptionalTrip::class, $result[0]);
        $this->assertSame('Miasto Zakynthos nocą', $result[0]->getTitle());
        $this->assertSame([], $result[0]->getDescription());
        $this->assertSame('https://itaka.seeplaces.com/pl/wycieczki/grecja/zakynthos/miasto-zakynthos-noca/', $result[0]->getUrl());
        $this->assertIsString($result[0]->getImage());
        $this->assertInstanceOf(Money::class, $result[0]->getMoney());
        $this->assertSame(93.03, $result[0]->getMoney()->getPrice());
        $this->assertSame(Currency::PLN, $result[0]->getMoney()->getCurrency());
    }

    public function testGetOptionalTripsBySearchTitle(): void
    {
        $result = $this->getOptionalTripItaka()->getOptionalTrips('kreta', 'grecja');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(OptionalTrip::class, $result[0]);
        $this->assertSame('Chania nocą', $result[0]->getTitle());
        $this->assertSame(['Co zobaczysz', 'Najpiękniejsze miasto na Krecie'], $result[0]->getDescription());
        $this->assertSame('https://itaka.seeplaces.com/pl/wycieczki/grecja/kreta-chania/chania-noca/', $result[0]->getUrl());
        $this->assertIsString($result[0]->getImage());
        $this->assertInstanceOf(Money::class, $result[0]->getMoney());
        $this->assertSame(170.55, $result[0]->getMoney()->getPrice());
        $this->assertSame(Currency::PLN, $result[0]->getMoney()->getCurrency());
    }

    /** @return string[][]|float[][] */
    public function getData(): array
    {
        return [
            ['od 97,23 zł /os.', 97.23],
            ['od 170,15 zł /os.', 170.15],
            ['od 194,46 zł /os.', 194.46],
        ];
    }

    /** @dataProvider getData */
    public function testParsePrice(string $input, float $expected): void
    {
        $this->assertSame($this->invokeMethod($this->getOptionalTripItaka(), 'parsePrice', [$input]), $expected);
    }
}
