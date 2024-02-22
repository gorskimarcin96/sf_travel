<?php

namespace App\Repository;

use App\Entity\City;

interface CityRepositoryInterface
{
    public function findByNamePl(string $name): ?City;
}
