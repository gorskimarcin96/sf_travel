<?php

namespace App\Utils\Helper;

final class Parser
{
    public function stringToFloat(string $text): float
    {
        return (float) filter_var($text, FILTER_SANITIZE_NUMBER_FLOAT);
    }
}
