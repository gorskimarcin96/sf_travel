<?php

namespace App\Exception;

final class BehatException extends \Exception
{
    public function __construct(string $string)
    {
        parent::__construct($string);
    }
}
