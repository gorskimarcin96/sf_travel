<?php

namespace App\Tests\Mocks\Repository;

use App\Entity\City;
use App\Repository\WeatherRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<\App\Entity\Weather>
 */
final class WeatherRepository extends ServiceEntityRepository implements WeatherRepositoryInterface
{
    #[\Override]
    public function findByCityAndBetweenDateAndSource(
        City $city,
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        string $source
    ): array {
        return [];
    }
}
