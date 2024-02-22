<?php

namespace App\Exception;

final class NotMatchedException extends \RuntimeException
{
    public function __construct(string $value)
    {
        parent::__construct(sprintf('Value %s is not matched.', $value));
    }
}
