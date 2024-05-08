<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\ApiResource\Input\LastMinute as Input;
use App\Entity\LastMinute;
use App\Exception\NullException;
use App\Factory\SearchServices;
use App\Message\LastMinute as Message;
use App\Repository\LastMinuteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;

final class LastMinuteController extends AbstractController
{
    /** @var class-string[] */
    private readonly array $todo;

    public function __construct(
        private readonly LastMinuteRepository $lastMinuteRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly ValidatorInterface $validator,
        SearchServices $tripServices
    ) {
        $this->todo = array_map(static fn (object $class): string => $class::class, $tripServices->createTrips());
    }

    public function __invoke(Input $input): LastMinute
    {
        $this->validator->validate($input);
        $lastMinute = $this->lastMinuteRepository->findByInput($input);

        if (!$lastMinute instanceof LastMinute || $input->isForce()) {
            $lastMinute = (new LastMinute())
                ->setFromAirport($input->getFromAirport())
                ->setAdults($input->getAdults())
                ->setChildren($input->getChildren())
                ->setHotelFoods($input->getHotelFoods())
                ->setHotelStars($input->getHotelStars())
                ->setHotelRate($input->getHotelRate())
                ->setRangeFrom($input->getRangeFrom())
                ->setRangeTo($input->getRangeTo())
                ->setTodo($this->todo);

            $lastMinute = $this->lastMinuteRepository->save($lastMinute);

            $this->messageBus->dispatch(new Message($lastMinute->getId() ?? throw new NullException()));
        }

        return $lastMinute;
    }
}
