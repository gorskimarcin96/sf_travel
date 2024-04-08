<?php

namespace App\Tests\Utils\Crawler\Flight;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\Common\Money;
use App\Utils\Crawler\Flight\Booking;
use App\Utils\Crawler\Flight\Model\Flight;
use App\Utils\Enum\Currency;

class BookingTest extends ContainerKernelTestCase
{
    public function testGetSource(): void
    {
        $this->assertSame(Booking::class, $this->getFlightBooking()->getSource());
    }

    public function testGetFlights(): void
    {
        $result = $this->getFlightBooking()->getFlights(
            'WAW',
            'ZTH',
            new \DateTimeImmutable('01-01-2020'),
            new \DateTimeImmutable('07-01-2020')
        );

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Flight::class, $result[0]);
        $this->assertSame('WAW', $result[0]->getFromAirport());
        $this->assertSame(1706800500, $result[0]->getFromStart()->getTimestamp());
        $this->assertSame(1706869800, $result[0]->getFromEnd()->getTimestamp());
        $this->assertSame(1, $result[0]->getFromStops());
        $this->assertSame('ZTH', $result[0]->getToAirport());
        $this->assertSame(1709215200, $result[0]->getToStart()->getTimestamp());
        $this->assertSame(1709302800, $result[0]->getToEnd()->getTimestamp());
        $this->assertSame(1, $result[0]->getToStops());
        $this->assertInstanceOf(Money::class, $result[0]->getMoney());
        $this->assertSame(2909.21, $result[0]->getMoney()->getPrice());
        $this->assertSame(Currency::PLN, $result[0]->getMoney()->getCurrency());
        $this->assertSame(false, $result[0]->getMoney()->isPriceForOnePerson());
        $this->assertSame(
            'https://flights.booking.com/flights/WAW-ZTH/?type=ROUNDTRIP&cabinClass=ECONOMY&sort=BEST&travelPurpose=leisure&adults=2&children=0&from=WAW&to=ZTH&depart=2020-01-01&return=2020-01-07',
            $result[0]->getUrl()
        );
    }
}
