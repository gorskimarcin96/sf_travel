<?php

namespace App\Tests\Mocks;

use App\Utils\Api\Translation\TranslationInterface;

final class Translation implements TranslationInterface
{
    #[\Override]
    public function translate(string $text, string $targetLang, ?string $sourceLang = null): array
    {
        return [new \App\Utils\Api\Translation\Model\Translation($text)];
    }
}
