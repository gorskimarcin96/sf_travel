<?php

namespace App\Utils\File;

use App\Exception\FileNotExistsException;
use App\Exception\ReadFileException;

final readonly class FileManager implements FileManagerInterface
{
    public function __construct(private string $projectDir)
    {
    }

    /**
     * @param array<string, array<string, bool>> $options
     */
    #[\Override]
    public function read(string $path, array $options = []): string
    {
        if (!str_starts_with($path, 'http')) {
            $path = sprintf('%s/%s', $this->projectDir, $path);

            if (!file_exists($path)) {
                throw new FileNotExistsException($path);
            }
        }

        return file_get_contents($path, false, stream_context_create($options)) ?: throw new ReadFileException($path);
    }
}
