<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Weather;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Weather>
 *
 * @method Weather|null find($id, $lockMode = null, $lockVersion = null)
 * @method Weather|null findOneBy(array $criteria, array $orderBy = null)
 * @method Weather[]    findAll()
 * @method Weather[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class WeatherRepository extends ServiceEntityRepository implements WeatherRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Weather::class);
    }

    /**
     * @return Weather[]
     */
    #[\Override]
    public function findByCityAndBetweenDateAndSource(
        City $city,
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        string $source
    ): array {
        return $this->createQueryBuilder('w')
            ->where('w.city = :city')
            ->andWhere('w.date >= :from')
            ->andWhere('w.date <= :to')
            ->andWhere('w.source = :source')
            ->setParameters([
                'city' => $city,
                'from' => $from,
                'to' => $to,
                'source' => $source,
            ])
            ->getQuery()
            ->getResult();
    }
}
