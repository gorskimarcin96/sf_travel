<?php

namespace App\Command;

use App\Entity\Search;
use App\Exception\NullException;
use App\Factory\TripServices;
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
        TripServices $factory,
        private readonly EntityManagerInterface $entityManager,
        private readonly SearchHandler $searchHandler,
    ) {
        parent::__construct();

        $this->services = array_map(static fn (object $class): string => $class::class, $factory->create());
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
         * @var string                      $nation
         * @var string                      $place
         * @var class-string|class-string[] $services
         */
        [$nation, $place, $services] = [
            $input->getArgument('nation'),
            $input->getArgument('place'),
            $input->getArgument('service') ?: $this->services,
        ];
        $search = (new Search())
            ->setNation($nation)
            ->setPlace($place)
            ->setFrom(new \DateTimeImmutable())
            ->setTo((new \DateTimeImmutable())->modify('+7 days'))
            ->setAdults(2)
            ->setChildren(0)
            ->setTodo(is_string($services) ? [$services] : $services);

        $this->entityManager->persist($search);
        $this->entityManager->flush();

        $io = new SymfonyStyle($input, $output);
        $progressBar = new ProgressBar($output, count($search->getTodo()));
        $progressBar->start();

        do {
            $message = new \App\Message\Search($search->getId() ?? throw new NullException());

            $this->searchHandler->__invoke($message, false);
            $progressBar->advance();
        } while (!$search->isFinished());

        $progressBar->finish();
        $io->newLine();

        return Command::SUCCESS;
    }
}
