<?php

namespace App\Tests\Factory;

use App\Factory\TripServices;
use App\Utils\Crawler\OptionalTrip\Tui;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TripServicesTest extends KernelTestCase
{
    private TripServices $tripServices;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var TripServices $tripServices */
        $tripServices = self::getContainer()->get(TripServices::class);
        $this->tripServices = $tripServices;
    }

    public function testCreate(): void
    {
        $this->assertIsArray($this->tripServices->create());
    }

    public function testFindByClassName(): void
    {
        $this->assertInstanceOf(Tui::class, $this->tripServices->findByClassName(Tui::class));
    }

    public function testFindByClassNameFailed(): void
    {
        $this->expectException(\LogicException::class);

        $this->tripServices->findByClassName(\stdClass::class);
    }
}
