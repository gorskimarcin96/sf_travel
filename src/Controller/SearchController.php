<?php

namespace App\Controller;

use App\ApiResource\Input\Search as Input;
use App\Entity\Search;
use App\Exception\NullException;
use App\Factory\TripServices;
use App\Repository\SearchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;

final class SearchController extends AbstractController
{
    /** @var string[] */
    private readonly array $todo;

    public function __construct(
        private readonly SearchRepository $repository,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus,
        TripServices $factory
    ) {
        $this->todo = array_map(static fn (object $class): string => $class::class, $factory->create());
    }

    public function __invoke(Input $input): Search
    {
        $search = $this->repository->findByNationAndPlace($input->getNation(), $input->getPlace());

        if (!$search || $input->isForce()) {
            $search = (new Search())
                ->setNation($input->getNation())
                ->setPlace($input->getPlace())
                ->setTodo($this->todo);

            $this->entityManager->persist($search);
            $this->entityManager->flush();

            $this->messageBus->dispatch(new \App\Message\Search($search->getId() ?? throw new NullException()));
        }

        return $search;
    }
}