<?php

namespace App\Exception;

class NullException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Value is null.');
    }
}
