<?php

namespace App\Utils\Api\Translation;

use App\Utils\Api\Translation\Model\Translation;

interface TranslationInterface
{
    /**
     * @return Translation[]
     */
    public function translate(string $text, string $targetLang, ?string $sourceLang = null): array;
}
