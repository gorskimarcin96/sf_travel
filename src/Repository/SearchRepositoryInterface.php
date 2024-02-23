<?php

namespace App\Repository;

use App\ApiResource\Input\Search as Input;
use App\Entity\Search;

interface SearchRepositoryInterface
{
    /** @phpstan-ignore-next-line */
    public function find(int $id, ?int $lockMode = null, ?int $lockVersion = null);

    public function findByInput(Input $input): ?Search;
}
