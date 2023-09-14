<?php

namespace App\Exception;

final class NationRequiredException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Nation value is required.');
    }
}
