<?php

namespace App\Tests\Utils\Crawler\PageAttraction;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\PageAttraction\BliskoCorazDalej;

class BliskoCorazDalejTest extends ContainerKernelTestCase
{
    public function testGetSource(): void
    {
        $this->assertSame(BliskoCorazDalej::class, $this->getBliskoCorazDalej()->getSource());
    }

    public function testGetPages(): void
    {
        $result = $this->getBliskoCorazDalej()->getPages('zakynthos', 'grecja');

        $this->assertCount(0, $result);
    }
}
