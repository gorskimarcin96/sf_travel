<?php

namespace App\Tests\Utils\Crawler\FlyCodes;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\FlyCodes\Model\FlyCode;

class WikipediaTest extends ContainerKernelTestCase
{
    public function testGetFlyCodes(): void
    {
        $result = iterator_to_array($this->getFlyCodesWikipedia()->getFlyCodes());

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertInstanceOf(FlyCode::class, $result[0]);
        $this->assertSame('AAA', $result[0]->getCode());
        $this->assertSame('Anaa', $result[0]->getCity());
        $this->assertSame('Polinezja Francuska', $result[0]->getNation());
    }
}
