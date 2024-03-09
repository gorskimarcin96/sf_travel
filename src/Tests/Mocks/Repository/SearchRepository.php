<?php

namespace App\Tests\Mocks\Repository;

use App\ApiResource\Input\Search as Input;
use App\Entity\Search;
use App\Repository\SearchRepositoryInterface;
use App\Tests\Mocks\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Search>
 */
final class SearchRepository extends ServiceEntityRepository implements SearchRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, string $entityClass, private readonly EntityManager $entityManager)
    {
        parent::__construct($registry, $entityClass);
    }

    #[\Override]
    public function find($id, $lockMode = null, $lockVersion = null): ?Search
    {
        $entities = $this->entityManager->getFlushEntities();
        $entities = array_filter($entities, static fn (object $entity): bool => $entity instanceof Search);

        foreach ($entities as $entity) {
            if ($entity->getId() === $id) {
                return $entity;
            }
        }

        return null;
    }

    #[\Override]
    public function findByInput(Input $input): ?Search
    {
        return null;
    }

    #[\Override]
    public function updateFinished(int $id): void
    {
    }
}
