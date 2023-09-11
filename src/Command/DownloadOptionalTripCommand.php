<?php

namespace App\Command;

use App\Entity\OptionalTrip;
use App\Utils\Crawler\OptionalTrip\OptionalTripInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:download:optional-trips')]
class DownloadOptionalTripCommand extends Command
{
    /** @var OptionalTripInterface[] */
    private readonly array $optionalTrips;

    public function __construct(
        \IteratorAggregate $optionalTrips,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();

        // @phpstan-ignore-next-line
        $this->optionalTrips = iterator_to_array($optionalTrips);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('nation', InputArgument::REQUIRED, 'Nation')
            ->addArgument('place', InputArgument::REQUIRED, 'Place')
            ->addArgument('service', InputArgument::OPTIONAL, 'Service');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var string $place
         * @var string $nation
         * @var string $service
         */
        [$io, $place, $nation, $service] = [
            new SymfonyStyle($input, $output),
            $input->getArgument('place'),
            $input->getArgument('nation'),
            $input->getArgument('service'),
        ];

        foreach ($this->optionalTrips as $optionalTrip) {
            if ($service && $optionalTrip->getSource() !== $service) {
                continue;
            }

            /** @var OptionalTrip[] $models */
            $models = $optionalTrip->getOptionalTrips($place, $nation);

            $this->logger->notice(sprintf('Get %s trips from "%s".', count($models), $optionalTrip->getSource()));

            foreach ($models as $model) {
                $optionalTrip = (new OptionalTrip())
                    ->setTitle($model->getTitle())
                    ->setDescription($model->getDescription())
                    ->setUrl($model->getUrl())
                    ->setImg($model->getImg())
                    ->setSource($optionalTrip->getSource())
                    ->setMoney($model->getMoney());

                $this->entityManager->persist($optionalTrip);
            }
        }

        $this->entityManager->flush();
        $io->success(sprintf('Trips is downloaded for "%s" - "%s".', $place, $nation));

        return Command::SUCCESS;
    }
}
