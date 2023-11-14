<?php

namespace App\Exception;

final class EmptyStringException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('String is empty.');
    }
}
