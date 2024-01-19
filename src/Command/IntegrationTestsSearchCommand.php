<?php

namespace App\Command;

use App\Entity\Search;
use App\Exception\NullException;
use App\Factory\SearchServices;
use App\MessageHandler\SearchHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:integration-tests:search',
    description: 'Testing download search data.',
)]
final class IntegrationTestsSearchCommand extends Command
{
    /** @var class-string[] */
    private readonly array $services;

    public function __construct(
        SearchServices $tripServices,
        private readonly EntityManagerInterface $entityManager,
        private readonly SearchHandler $searchHandler,
    ) {
        parent::__construct();

        $this->services = array_map(static fn (object $class): string => $class::class, $tripServices->create());
    }

    #[\Override] protected function configure(): void
    {
        $this
            ->addArgument('nation', InputArgument::REQUIRED, 'Nation')
            ->addArgument('place', InputArgument::REQUIRED, 'Place')
            ->addArgument('fromAirport', InputArgument::OPTIONAL, 'From airport')
            ->addArgument('toAirport', InputArgument::OPTIONAL, 'To airport')
            ->addArgument('service', InputArgument::OPTIONAL, 'Service');
    }

    #[\Override] protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var string                      $nation
         * @var string                      $place
         * @var string                      $fromAirport
         * @var string                      $toAirport
         * @var class-string|class-string[] $services
         */
        [$nation, $place, $fromAirport, $toAirport, $services] = [
            $input->getArgument('nation'),
            $input->getArgument('place'),
            $input->hasArgument('fromAirport') ? $input->getArgument('fromAirport') : null,
            $input->hasArgument('toAirport') ? $input->getArgument('toAirport') : null,
            $input->getArgument('service') ?: $this->services,
        ];
        $search = (new Search())
            ->setNation($nation)
            ->setPlace($place)
            ->setFrom((new \DateTimeImmutable())->modify('+14 days'))
            ->setTo((new \DateTimeImmutable())->modify('+21 days'))
            ->setFromAirport($fromAirport)
            ->setToAirport($toAirport)
            ->setAdults(2)
            ->setChildren(0)
            ->setTodo(is_string($services) ? [$services] : $services);

        $this->entityManager->persist($search);
        $this->entityManager->flush();

        $symfonyStyle = new SymfonyStyle($input, $output);
        $progressBar = new ProgressBar($output, count($search->getTodo()));
        $progressBar->start();

        do {
            $message = new \App\Message\Search($search->getId() ?? throw new NullException());

            $this->searchHandler->__invoke($message, false, true);
            $progressBar->advance();
        } while (!$search->isFinished());

        $progressBar->finish();
        $symfonyStyle->newLine();

        return Command::SUCCESS;
    }
}
