<?php

namespace App\Tests\Factory;

use App\Entity\SourceInterface;
use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\OptionalTrip\Itaka;

class TripServicesTest extends ContainerKernelTestCase
{
    public function testCreate(): void
    {
        $data = $this->getSearchServices()->create();

        $this->assertIsArray($data);
        $this->assertInstanceOf(SourceInterface::class, $data[0]);
    }

    public function testCreateTrips(): void
    {
        $data = $this->getSearchServices()->createTrips();

        $this->assertIsArray($data);
        $this->assertInstanceOf(SourceInterface::class, $data[0]);
    }

    public function testFindByClassName(): void
    {
        $this->assertInstanceOf(Itaka::class, $this->getSearchServices()->findByClassName(Itaka::class));
    }

    public function testFindByClassNameFailed(): void
    {
        $this->expectException(\LogicException::class);

        $this->getSearchServices()->findByClassName(\stdClass::class);
    }
}
