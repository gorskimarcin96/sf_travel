<?php

namespace App\Tests\Utils\Crawler\PageAttraction;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\PageAttraction\GuruPodrozy;

class GuruPodrozyTest extends ContainerKernelTestCase
{
    public function testGetSource(): void
    {
        $this->assertSame(GuruPodrozy::class, $this->getGuruPodrozy()->getSource());
    }

    public function testGetPages(): void
    {
        $result = $this->getGuruPodrozy()->getPages('zakynthos', 'grecja');

        $this->assertCount(0, $result);
    }
}
