<?php

namespace App\Exception;

final class NullException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Value is null.');
    }
}
