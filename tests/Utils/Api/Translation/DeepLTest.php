<?php

namespace App\Tests\Utils\Api\Translation;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Api\Translation\Model\Translation;

class DeepLTest extends ContainerKernelTestCase
{
    public function testTranslate(): void
    {
        $result = $this->getTranslationDeepL()->translate('Warsaw', 'pl');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Translation::class, $result[0]);
        $this->assertSame('Warszawa', $result[0]->getText());
    }
}
