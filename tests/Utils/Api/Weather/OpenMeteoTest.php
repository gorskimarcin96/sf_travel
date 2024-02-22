<?php

namespace App\Tests\Utils\Api\Weather;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Api\Weather\Model\Weather;
use App\Utils\Api\Weather\OpenMeteo;

class OpenMeteoTest extends ContainerKernelTestCase
{
    public function testGetSource(): void
    {
        $this->assertSame(OpenMeteo::class, $this->getWeatherOpenMeteo()->getSource());
    }

    public function testGetByCityAndBetweenDate(): void
    {
        $result = $this->getWeatherOpenMeteo()->getByCityAndBetweenDate(
            'Zakynthos',
            new \DateTimeImmutable('2020-01-01'),
            new \DateTimeImmutable('2020-01-07')
        );

        $this->assertIsArray($result);
        $this->assertCount(6, $result);
        $this->assertInstanceOf(Weather::class, $result[0]);
        $this->assertInstanceOf(\DateTimeInterface::class, $result[0]->getDate());
        $this->assertSame(1577836800, $result[0]->getDate()->getTimestamp());
        $this->assertSame(0.0, $result[0]->getPrecipitationHours());
        $this->assertSame(0.0, $result[0]->getPrecipitationSum());
        $this->assertSame(11.9, $result[0]->getTemperature2mMean());
    }
}
