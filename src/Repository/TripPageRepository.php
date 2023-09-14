<?php

namespace App\Repository;

use App\Entity\TripPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TripPage>
 *
 * @method TripPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TripPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TripPage[]    findAll()
 * @method TripPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class TripPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TripPage::class);
    }
}
