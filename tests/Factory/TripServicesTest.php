<?php

namespace App\Tests\Factory;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\OptionalTrip\Itaka;

class TripServicesTest extends ContainerKernelTestCase
{
    public function testCreate(): void
    {
        $this->assertIsArray($this->getSearchServices()->create());
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
