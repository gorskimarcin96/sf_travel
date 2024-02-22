<?php

namespace App\Tests\Utils\Crawler\PageAttraction;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\PageAttraction\TysiacStronSwiata;

class TysiacStronSwiataTest extends ContainerKernelTestCase
{
    public function testGetSource(): void
    {
        $this->assertSame(TysiacStronSwiata::class, $this->getTysiacStronSwiata()->getSource());
    }

    public function testGetPages(): void
    {
        $result = $this->getTysiacStronSwiata()->getPages('zakynthos', 'grecja');

        $this->assertCount(0, $result);
    }
}
