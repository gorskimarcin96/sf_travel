<?php

namespace App\Repository;

use App\Entity\TripArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TripArticle>
 *
 * @method TripArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method TripArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method TripArticle[]    findAll()
 * @method TripArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TripArticle::class);
    }
}
