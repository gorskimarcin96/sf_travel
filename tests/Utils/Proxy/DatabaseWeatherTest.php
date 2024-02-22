<?php

namespace App\Tests\Utils\Proxy;

use App\Entity\Weather;
use App\Tests\ContainerKernelTestCase;

class DatabaseWeatherTest extends ContainerKernelTestCase
{
    public function testGetByCityAndBetweenDate(): void
    {
        $weathers = $this->getProxyDatabaseWeather()->getByCityAndBetweenDate(
            $this->getWeatherOpenMeteo(),
            'Zakynthos',
            new \DateTimeImmutable('2020-01-01'),
            new \DateTimeImmutable('2020-01-07')
        );

        $this->assertIsArray($weathers);
        $this->assertCount(6, $weathers);
        $this->assertInstanceOf(Weather::class, $weathers[0]);
        $this->assertInstanceOf(\DateTimeInterface::class, $weathers[0]->getDate());
        $this->assertSame(1577836800, $weathers[0]->getDate()->getTimestamp());
        $this->assertSame(0.0, $weathers[0]->getPrecipitationHours());
        $this->assertSame(0.0, $weathers[0]->getPrecipitationSum());
        $this->assertSame(11.9, $weathers[0]->getTemperature2mMean());
    }
}
