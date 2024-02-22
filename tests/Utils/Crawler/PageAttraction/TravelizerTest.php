<?php

namespace App\Tests\Utils\Crawler\PageAttraction;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\PageAttraction\Travelizer;

class TravelizerTest extends ContainerKernelTestCase
{
    public function testGetSource(): void
    {
        $this->assertSame(Travelizer::class, $this->getTravelizer()->getSource());
    }

    public function testGetPages(): void
    {
        $result = $this->getTravelizer()->getPages('zakynthos', 'grecja');

        $this->assertCount(0, $result);
    }
}
