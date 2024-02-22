<?php

namespace App\Tests\Utils\Crawler\PageAttraction;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\PageAttraction\TasteAway;

class TasteAwayTest extends ContainerKernelTestCase
{
    public function testGetSource(): void
    {
        $this->assertSame(TasteAway::class, $this->getTasteAway()->getSource());
    }

    public function testGetPages(): void
    {
        $result = $this->getTasteAway()->getPages('zakynthos', 'grecja');

        $this->assertCount(0, $result);
    }
}
