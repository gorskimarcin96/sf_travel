<?php

namespace App\Tests\Utils\Saver;

use App\Entity\Search;
use App\Entity\Weather as EntityWeather;
use App\Tests\ContainerKernelTestCase;
use App\Utils\Api\Weather\Model\Weather;
use App\Utils\Api\Weather\WeatherInterface;

class WeatherTest extends ContainerKernelTestCase
{
    public function testSave(): void
    {
        $this->getEntityManager(true);

        $weatherService = $this->createMock(WeatherInterface::class);
        $weatherService
            ->method('getByCityAndBetweenDate')
            ->willReturn([
                new Weather(new \DateTime('01-01-2000'), 0.1, 0.2, 0.3),
                new Weather(new \DateTime('02-01-2000'), 0, 0, 0),
                new Weather(new \DateTime('03-01-2000'), 0, 0, 0),
                new Weather(new \DateTime('04-01-2000'), 0, 0, 0),
                new Weather(new \DateTime('05-01-2000'), 0, 0, 0),
                new Weather(new \DateTime('06-01-2000'), 0, 0, 0),
                new Weather(new \DateTime('07-01-2000'), 0, 0, 0),
            ]);
        $weatherService
            ->method('getSource')
            ->willReturn(__CLASS__);
        $search = (new Search())
            ->setPlace('Zakynthos')
            ->setFrom(new \DateTimeImmutable('01-01-2020'))
            ->setTo(new \DateTimeImmutable('07-01-2020'));

        $this->getSaverWeather()->save($weatherService, $search);

        /** @var EntityWeather[] $weatherEntities */
        $weatherEntities = $this->getEntityManager()->getFlushEntities(EntityWeather::class);

        $this->assertCount(35, $weatherEntities);
        $this->assertInstanceOf(EntityWeather::class, $weatherEntities[0]);
        $this->assertSame(946684800, $weatherEntities[0]->getDate()->getTimestamp());
        $this->assertSame(0.1, $weatherEntities[0]->getTemperature2mMean());
        $this->assertSame(0.2, $weatherEntities[0]->getPrecipitationHours());
        $this->assertSame(0.3, $weatherEntities[0]->getPrecipitationSum());
    }
}
