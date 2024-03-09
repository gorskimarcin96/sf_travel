<?php

namespace App\Utils\Helper;

use App\Utils\File\FileManagerInterface;

final readonly class Base64
{
    public function __construct(private FileManagerInterface $fileManager)
    {
    }

    public function convertFromImage(string $path): string
    {
        $content = $this->fileManager->read($path, [
            'ssl' => [
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        return 'data:image/'.pathinfo($path, PATHINFO_EXTENSION).';base64,'.base64_encode($content);
    }
}
