<?php

namespace App\Utils\Helper;

use App\Utils\File\FileManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class Base64
{
    public function __construct(private FileManagerInterface $fileManager, private LoggerInterface $logger)
    {
    }

    public function convertFromImage(string $path): ?string
    {
        try {
            $content = $this->fileManager->read($path, ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);

            return 'data:image/'.pathinfo($path, PATHINFO_EXTENSION).';base64,'.base64_encode($content);
        } catch (\Throwable $throwable) {
            $this->logger->error(sprintf('%s: %s', $throwable::class, $throwable->getMessage()));

            return null;
        }
    }
}
