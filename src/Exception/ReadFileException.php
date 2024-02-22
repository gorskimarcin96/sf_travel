<?php

namespace App\Exception;

final class ReadFileException extends FileException
{
    public function __construct(private readonly string $path)
    {
        parent::__construct(sprintf('Problem with read file from "%s".', $this->path));
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
