<?php

namespace App\Repository;

use App\ApiResource\Input\LastMinute as Input;
use App\Entity\LastMinute;

interface LastMinuteRepositoryInterface
{
    /** @phpstan-ignore-next-line */
    public function find(int $id, ?int $lockMode = null, ?int $lockVersion = null);

    public function findByInput(Input $input): ?LastMinute;

    public function save(LastMinute $lastMinute, bool $flush = false): LastMinute;
}
