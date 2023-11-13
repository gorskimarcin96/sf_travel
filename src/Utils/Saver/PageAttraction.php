<?php

namespace App\Utils\Saver;

use App\Entity\Search;
use App\Entity\TripArticle;
use App\Entity\TripPage;
use App\Utils\Crawler\Model\URLTrait;
use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Crawler\PageAttraction\Model\Page as Model;
use App\Utils\Crawler\PageAttraction\PageAttractionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class PageAttraction
{
    use URLTrait;

    public function __construct(
        private LoggerInterface $downloaderLogger,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function save(PageAttractionInterface $service, string $place, string $nation, Search $search): Search
    {
        $models = $service->getPages($place, $nation);
        $this->downloaderLogger->info(sprintf('Get %s pages from "%s".', count($models), $service->getSource()));

        /** @var Model[] $models */
        $models = $this->uniqueByUrl($models);
        $this->downloaderLogger->info(sprintf('Unique models %s.', count($models)));

        foreach ($models as $model) {
            /** @var Page $model */
            $pageTrip = (new TripPage())
                ->setSearch($search)
                ->setUrl($model->getUrl())
                ->setSource($service->getSource())
                ->setMap($model->getMap());

            foreach ($model->getArticles() as $article) {
                $articleTrip = (new TripArticle())
                    ->setTitle($article->getTitle())
                    ->setDescriptions($article->getDescriptions())
                    ->setImages($article->getImages());

                $pageTrip->addTripArticle($articleTrip);
            }

            $this->entityManager->persist($pageTrip);

            $search->addTripPage($pageTrip);
        }

        return $search;
    }
}
