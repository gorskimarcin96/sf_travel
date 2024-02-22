<?php

namespace App\Tests\Mocks;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Cache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;

final class EntityManager implements EntityManagerInterface
{
    /**
     * @var object[]
     */
    private array $persistEntities = [];
    /**
     * @var object[]
     */
    private array $flushEntities = [];

    #[\Override]
    public function getRepository($className): EntityRepository
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getCache(): ?Cache
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getConnection(): Connection
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getExpressionBuilder(): Expr
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function beginTransaction(): void
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function transactional($func): mixed
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function commit(): void
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function rollback(): void
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function createQuery($dql = ''): Query
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function createNamedQuery($name): Query
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function createNativeQuery($sql, ResultSetMapping $rsm): NativeQuery
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function createNamedNativeQuery($name): NativeQuery
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function createQueryBuilder(): QueryBuilder
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getReference($entityName, $id): ?object
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getPartialReference($entityName, $identifier): ?object
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function close(): void
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function copy($entity, $deep = false): object
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function lock($entity, $lockMode, $lockVersion = null): void
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getEventManager(): EventManager
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getConfiguration(): Configuration
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function isOpen(): bool
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getUnitOfWork(): UnitOfWork
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getHydrator($hydrationMode): AbstractHydrator
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function newHydrator($hydrationMode): AbstractHydrator
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getProxyFactory(): ProxyFactory
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getFilters(): FilterCollection
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function isFiltersStateClean(): bool
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function hasFilters(): bool
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getClassMetadata($className): ClassMetadata
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function find(string $className, $id): object
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function persist(object $object): void
    {
        $this->persistEntities[] = $object;
    }

    #[\Override]
    public function remove(object $object): void
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function clear(): void
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function detach(object $object): void
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function flush(): void
    {
        $this->flushEntities = [...$this->persistEntities, ...$this->flushEntities];

        $this->persistEntities = [];
    }

    #[\Override]
    public function initializeObject(object $obj): void
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function contains(object $object): bool
    {
        throw new \LogicException('Method is not implemented.');
    }

    /** @phpstan-ignore-next-line */
    #[\Override]
    public function getMetadataFactory(): ClassMetadataFactory
    {
        throw new \LogicException('Method is not implemented.');
    }

    public function wrapInTransaction(callable $func): mixed
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function refresh(object $object, int $lockMode = null): void
    {
        throw new \LogicException('Method is not implemented.');
    }

    /**
     * @return object[]
     */
    public function getFlushEntities(string $className = null): array
    {
        return null === $className ?
            $this->flushEntities :
            array_values(array_filter($this->flushEntities, static fn (object $entity): bool => $entity instanceof $className));
    }

    public function reset(): void
    {
        $this->persistEntities = $this->flushEntities = [];
    }

    /**
     * @param object[] $entities
     */
    public function loadEntities(array $entities): void
    {
        $this->flushEntities += $entities;
    }
}
