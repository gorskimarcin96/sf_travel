<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
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
        private readonly ValidatorInterface $validator,
        TripServices $factory
    ) {
        $this->todo = array_map(static fn (object $class): string => $class::class, $factory->create());
    }

    public function __invoke(Input $input): Search
    {
        $this->validator->validate($input);
        $search = $this->repository->findByInput($input);

        if (!$search || $input->isForce()) {
            $search = (new Search())
                ->setNation($input->getNation())
                ->setPlace($input->getPlace())
                ->setFrom($input->getFrom())
                ->setTo($input->getTo())
                ->setAdults($input->getAdults())
                ->setChildren($input->getChildren())
                ->setTodo($this->todo);

            $this->entityManager->persist($search);
            $this->entityManager->flush();

            $this->messageBus->dispatch(new \App\Message\Search($search->getId() ?? throw new NullException()));
        }

        return $search;
    }
}
