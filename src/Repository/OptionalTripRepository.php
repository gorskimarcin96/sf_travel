<?php

namespace App\Repository;

use App\Entity\OptionalTrip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OptionalTrip>
 *
 * @method OptionalTrip|null find($id, $lockMode = null, $lockVersion = null)
 * @method OptionalTrip|null findOneBy(array $criteria, array $orderBy = null)
 * @method OptionalTrip[]    findAll()
 * @method OptionalTrip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class OptionalTripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, OptionalTrip::class);
    }
}
