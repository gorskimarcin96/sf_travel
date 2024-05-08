<?php

namespace App\Repository;

use App\ApiResource\Input\LastMinute as Input;
use App\Entity\LastMinute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LastMinute>
 *
 * @method LastMinute|null find($id, $lockMode = null, $lockVersion = null)
 * @method LastMinute|null findOneBy(array $criteria, array $orderBy = null)
 * @method LastMinute[]    findAll()
 * @method LastMinute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class LastMinuteRepository extends ServiceEntityRepository implements LastMinuteRepositoryInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, LastMinute::class);
    }

    #[\Override]
    public function findByInput(Input $input): ?LastMinute
    {
        $query = $this->createQueryBuilder('lm')
            ->andWhere('lm.adults = :adults')
            ->andWhere('lm.children = :children')
            ->setParameters([
                'adults' => $input->getAdults(),
                'children' => $input->getChildren(),
            ]);

        if ($input->getFrom() instanceof \DateTimeImmutable) {
            $query->andWhere('lm.from = :from')->setParameter('from', $input->getFrom());
        }

        if ($input->getTo() instanceof \DateTimeImmutable) {
            $query->andWhere('lm.to = :to')->setParameter('to', $input->getTo());
        }

        if (null !== $input->getFromAirport() && '' !== $input->getFromAirport()) {
            $query->andWhere('lm.fromAirport = :fromAirport')->setParameter('fromAirport', $input->getFromAirport());
        }

        if (null !== $input->getHotelStars() && 0 !== $input->getHotelStars()) {
            $query->andWhere('lm.hotelStars = :hotelStars')->setParameter('hotelStars', $input->getHotelStars());
        }

        if ($input->getHotelRate()) {
            $query->andWhere('lm.hotelRate = :hotelRate')->setParameter('hotelRate', $input->getHotelRate());
        }

        if (null !== $input->getRangeFrom() && 0 !== $input->getRangeFrom()) {
            $query->andWhere('lm.rangeFrom = :rangeFrom')->setParameter('rangeFrom', $input->getRangeFrom());
        }

        if (null !== $input->getRangeTo() && 0 !== $input->getRangeTo()) {
            $query->andWhere('lm.rangeTo = :rangeTo')->setParameter('rangeTo', $input->getRangeTo());
        }

        if ([] !== $input->getHotelFoods()) {
            $query->setParameter('true', true);
        }

        foreach ($input->getHotelFoods() as $key => $hotelFood) {
            $query
                ->andWhere(sprintf('JSONB_EXISTS(lm.hotelFoods, :hotelFood_%s) = :true', $key))
                ->setParameter(sprintf('hotelFood_%s', $key), $hotelFood->value);
        }

        return $query->orderBy('lm.createdAt', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    #[\Override]
    public function save(LastMinute $lastMinute, bool $flush = false): LastMinute
    {
        $this->_em->persist($lastMinute);

        if ($flush) {
            $this->_em->flush();
        }

        return $lastMinute;
    }
}
