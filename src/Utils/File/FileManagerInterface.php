<?php

namespace App\Utils\File;

interface FileManagerInterface
{
    /**
     * @param array<string, array<string, bool>> $options
     */
    public function read(string $path, array $options = []): string;
}
