<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\ApiResource\Input\Search as Input;
use App\Entity\Search;
use App\Exception\NullException;
use App\Factory\SearchServices;
use App\Repository\SearchRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;

final class SearchController extends AbstractController
{
    /** @var class-string[] */
    private readonly array $todo;

    public function __construct(
        private readonly SearchRepositoryInterface $searchRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus,
        private readonly ValidatorInterface $validator,
        SearchServices $tripServices
    ) {
        $this->todo = array_map(static fn (object $class): string => $class::class, $tripServices->create());
    }

    public function __invoke(Input $input): Search
    {
        $this->validator->validate($input);
        $search = $this->searchRepository->findByInput($input);

        if (!$search instanceof Search || $input->isForce()) {
            $search = (new Search())
                ->setNation(strtolower($input->getNation()))
                ->setPlace(strtolower($input->getPlace()))
                ->setFrom($input->getFrom())
                ->setTo($input->getTo())
                ->setFromAirport($input->getFromAirport())
                ->setToAirport($input->getToAirport())
                ->setAdults($input->getAdults())
                ->setChildren($input->getChildren())
                ->setHotelFoods($input->getHotelFoods())
                ->setHotelStars($input->getHotelStars())
                ->setHotelRate($input->getHotelRate())
                ->setRangeFrom($input->getRangeFrom())
                ->setRangeTo($input->getRangeTo())
                ->setTodo($this->todo);

            $this->entityManager->persist($search);
            $this->entityManager->flush();

            $this->messageBus->dispatch(new \App\Message\Search($search->getId() ?? throw new NullException()));
        }

        return $search;
    }
}
