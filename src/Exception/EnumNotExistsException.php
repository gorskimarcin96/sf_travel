<?php

namespace App\Exception;

final class EnumNotExistsException extends \RuntimeException
{
    public function __construct(string $value)
    {
        parent::__construct(sprintf('Enum is not exists for value "%s".', $value));
    }
}
