<?php

namespace App\Tests\Mocks\Repository;

use App\ApiResource\Input\LastMinute as Input;
use App\Entity\LastMinute;
use App\Repository\LastMinuteRepositoryInterface;
use App\Tests\Mocks\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LastMinute>
 */
final class LastMinuteRepository extends ServiceEntityRepository implements LastMinuteRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, string $entityClass, private readonly EntityManager $entityManager)
    {
        parent::__construct($registry, $entityClass);
    }

    #[\Override]
    public function find($id, $lockMode = null, $lockVersion = null): ?LastMinute
    {
        $entities = $this->entityManager->getFlushEntities();
        $entities = array_filter($entities, static fn (object $entity): bool => $entity instanceof LastMinute);

        foreach ($entities as $entity) {
            if ($entity->getId() === $id) {
                return $entity;
            }
        }

        return null;
    }

    #[\Override]
    public function findByInput(Input $input): ?LastMinute
    {
        return null;
    }

    #[\Override]
    public function save(LastMinute $lastMinute, bool $flush = false): LastMinute
    {
        return $lastMinute;
    }
}
