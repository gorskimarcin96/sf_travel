<?php

namespace App\Repository;

use App\ApiResource\Input\Search as Input;
use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Search>
 *
 * @method Search|null find($id, $lockMode = null, $lockVersion = null)
 * @method Search|null findOneBy(array $criteria, array $orderBy = null)
 * @method Search[]    findAll()
 * @method Search[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SearchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Search::class);
    }

    public function findByInput(Input $input): ?Search
    {
        return $this->findOneBy([
            'nation' => strtolower($input->getNation()),
            'place' => strtolower($input->getPlace()),
            'from' => $input->getFrom(),
            'to' => $input->getTo(),
            'adults' => $input->getAdults(),
            'children' => $input->getChildren(),
        ], ['createdAt' => 'desc']);
    }
}
