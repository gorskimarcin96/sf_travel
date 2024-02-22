<?php

namespace App\Tests\Utils\Api\Geocoding;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Api\Geocoding\Model\Geocoding;

class OpenMeteoTest extends ContainerKernelTestCase
{
    public function testGetByCity(): void
    {
        $result = $this->getGeocodingOpenMeteo()->getByCity('Zakynthos');

        $this->assertInstanceOf(Geocoding::class, $result);
        $this->assertSame(251280, $result->getId());
        $this->assertSame('Zakynthos', $result->getName());
        $this->assertSame(37.78022, $result->getLatitude());
        $this->assertSame(20.89555, $result->getLongitude());
        $this->assertSame('Greece', $result->getCountry());
        $this->assertSame('GR', $result->getCountryCode());
    }
}
