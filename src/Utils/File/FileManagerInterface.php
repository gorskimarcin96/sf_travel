<?php

namespace App\Utils\File;

interface FileManagerInterface
{
    /**
     * @param array<string, array<string, boolean>> $options
     */
    public function read(string $path, array $options = []): string;
}
