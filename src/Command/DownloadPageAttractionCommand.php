<?php

namespace App\Command;

use App\Entity\TripArticle;
use App\Entity\TripPage;
use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Crawler\PageAttraction\PageAttractionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:download:page-attraction')]
class DownloadPageAttractionCommand extends Command
{
    /** @var PageAttractionInterface[] */
    private readonly array $pageAttractions;

    public function __construct(
        \IteratorAggregate $pageAttractions,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();

        // @phpstan-ignore-next-line
        $this->pageAttractions = iterator_to_array($pageAttractions);
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

        foreach ($this->pageAttractions as $pageAttraction) {
            if ($service && $pageAttraction->getSource() !== $service) {
                continue;
            }

            $models = $pageAttraction->getPages($place, $nation);

            $this->logger->notice(sprintf('Get %s pages from "%s".', count($models), $pageAttraction->getSource()));

            foreach ($models as $model) {
                /** @var Page $model */
                $pageTrip = (new TripPage())
                    ->setUrl($model->getUrl())
                    ->setSource($pageAttraction->getSource())
                    ->setMap($model->getMap());

                $this->entityManager->persist($pageTrip);

                foreach ($model->getArticles() as $article) {
                    $articleTrip = (new TripArticle())
                        ->setPage($pageTrip)
                        ->setTitle($article->getTitle())
                        ->setDescriptions($article->getDescriptions())
                        ->setImages($article->getImages());

                    $this->entityManager->persist($articleTrip);
                }
            }
        }

        $this->entityManager->flush();
        $io->success(sprintf('Pages is downloaded for "%s" - "%s".', $place, $nation));

        return Command::SUCCESS;
    }
}
