<?php

namespace App\Exception;

final class FileNotExistsException extends FileException
{
    public function __construct(private readonly string $path)
    {
        parent::__construct(sprintf('File "%s" is not exists.', $this->path));
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
