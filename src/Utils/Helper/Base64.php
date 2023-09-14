<?php

namespace App\Utils\Helper;

use App\Exception\FalseException;

final class Base64
{
    public function convertFromImage(string $path): string
    {
        $content = file_get_contents($path) ?: throw new FalseException();

        return 'data:image/'.pathinfo($path, PATHINFO_EXTENSION).';base64,'.base64_encode($content);
    }
}
