<?php

namespace App\Exception;

final class FalseException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Value is false.');
    }
}
