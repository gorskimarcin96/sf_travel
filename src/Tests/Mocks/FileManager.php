<?php

namespace App\Tests\Mocks;

use App\Utils\File\FileManagerInterface;

final class FileManager implements FileManagerInterface
{
    /**
     * @param array<string, array<string, boolean>> $options
     */
    #[\Override]
    public function read(string $path, array $options = []): string
    {
        return '';
    }
}
