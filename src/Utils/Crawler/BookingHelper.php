<?php

namespace App\Utils\Crawler;

trait BookingHelper
{
    public function createAttr(string $attr, string $tag = 'div', string $child = ''): string
    {
        return sprintf('%s[data-testid="%s"]%s', $tag, $attr, $child);
    }
}
