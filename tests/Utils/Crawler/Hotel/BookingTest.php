<?php

namespace App\Tests\Utils\Crawler\Hotel;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\Common\Money;
use App\Utils\Crawler\Hotel\Booking;
use App\Utils\Crawler\Hotel\Model\Hotel;
use App\Utils\Enum\Currency;
use App\Utils\Enum\Food;

class BookingTest extends ContainerKernelTestCase
{
    public function testGetSource(): void
    {
        $this->assertSame(Booking::class, $this->getHotelBooking()->getSource());
    }

    public function testGetHotels(): void
    {
        $from = new \DateTimeImmutable('01-01-2000');
        $to = new \DateTimeImmutable('07-01-2000');
        $result = $this->getHotelBooking()->getHotels('Zakynthos', $from, $to, 5, 7, [
            Food::ALL_INCLUSIVE,
            Food::BREAKFAST_LAUNCH_AND_DINNER,
            Food::BREAKFAST_AND_DINNER,
            Food::BREAKFAST,
        ], 4, 8, 2, 1);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Hotel::class, $result[0]);
        $this->assertSame('Alykanas Beach Grand Hotel by Zante Plaza', $result[0]->getTitle());
        $this->assertSame('#', $result[0]->getUrl());
        $this->assertSame(Food::ALL_INCLUSIVE, $result[0]->getFood());
        $this->assertSame(4, $result[0]->getStars());
        $this->assertSame(8.1, $result[0]->getRate());
        $this->assertIsString($result[0]->getImage());
        $this->assertSame('Alikanas', $result[0]->getAddress());
        $this->assertSame([
            'Pokój typu Superior',
            'All inclusive',
            'Na naszej stronie zostało tylko 7 w tej cenie',
        ], $result[0]->getDescriptions());
        $this->assertSame($from->getTimestamp(), $result[0]->getFrom()->getTimestamp());
        $this->assertSame($to->getTimestamp(), $result[0]->getTo()->getTimestamp());
        $this->assertInstanceOf(Money::class, $result[0]->getMoney());
        $this->assertSame(654.67, $result[0]->getMoney()->getPrice());
        $this->assertSame(Currency::PLN, $result[0]->getMoney()->getCurrency());
    }
}
