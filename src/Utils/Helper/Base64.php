<?php

namespace App\Utils\Helper;

use App\Exception\FalseException;

final class Base64
{
    public function convertFromImage(string $path): string
    {
        $options = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
        $content = file_get_contents($path, false, $options) ?: throw new FalseException();

        return 'data:image/'.pathinfo($path, PATHINFO_EXTENSION).';base64,'.base64_encode($content);
    }
}
