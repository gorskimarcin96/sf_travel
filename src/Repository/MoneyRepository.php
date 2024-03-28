<?php

namespace App\Repository;

use App\Utils\Crawler\Common\Money;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Money>
 *
 * @method Money|null find($id, $lockMode = null, $lockVersion = null)
 * @method Money|null findOneBy(array $criteria, array $orderBy = null)
 * @method Money[]    findAll()
 * @method Money[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MoneyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Money::class);
    }
}
