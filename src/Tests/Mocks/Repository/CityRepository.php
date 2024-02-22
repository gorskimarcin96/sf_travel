<?php

namespace App\Tests\Mocks\Repository;

use App\Entity\City;
use App\Repository\CityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<City>
 */
final class CityRepository extends ServiceEntityRepository implements CityRepositoryInterface
{
    #[\Override]
    public function findByNamePl(string $name): ?City
    {
        return null;
    }
}
