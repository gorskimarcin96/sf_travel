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
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Search::class);
    }

    public function findByInput(Input $input): ?Search
    {
        $query = $this->createQueryBuilder('s')
            ->andWhere('s.nation = :nation')
            ->andWhere('s.place = :place')
            ->andWhere('s.from = :from')
            ->andWhere('s.to = :to')
            ->andWhere('s.adults = :adults')
            ->andWhere('s.children = :children')
            ->setParameters([
                'nation' => strtolower($input->getNation()),
                'place' => strtolower($input->getPlace()),
                'from' => $input->getFrom(),
                'to' => $input->getTo(),
                'adults' => $input->getAdults(),
                'children' => $input->getChildren(),
            ]);

        if (null !== $input->getFromAirport() && '' !== $input->getFromAirport()) {
            $query->andWhere('s.fromAirport = :fromAirport')->setParameter('fromAirport', $input->getFromAirport());
        }

        if (null !== $input->getToAirport() && '' !== $input->getToAirport()) {
            $query->andWhere('s.toAirport = :toAirport')->setParameter('toAirport', $input->getToAirport());
        }

        if (null !== $input->getHotelStars() && 0 !== $input->getHotelStars()) {
            $query->andWhere('s.hotelStars = :hotelStars')->setParameter('hotelStars', $input->getHotelStars());
        }

        if ($input->getHotelRate()) {
            $query->andWhere('s.hotelRate = :hotelRate')->setParameter('hotelRate', $input->getHotelRate());
        }

        if (null !== $input->getRangeFrom() && 0 !== $input->getRangeFrom()) {
            $query->andWhere('s.rangeFrom = :rangeFrom')->setParameter('rangeFrom', $input->getRangeFrom());
        }

        if (null !== $input->getRangeTo() && 0 !== $input->getRangeTo()) {
            $query->andWhere('s.rangeTo = :rangeTo')->setParameter('rangeTo', $input->getRangeTo());
        }

        if ([] !== $input->getHotelFoods()) {
            $query->setParameter('true', true);
        }

        foreach ($input->getHotelFoods() as $key => $hotelFood) {
            $query
                ->andWhere(sprintf('JSONB_EXISTS(s.hotelFoods, :hotelFood_%s) = :true', $key))
                ->setParameter(sprintf('hotelFood_%s', $key), $hotelFood->value);
        }

        return $query->orderBy('s.createdAt', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
