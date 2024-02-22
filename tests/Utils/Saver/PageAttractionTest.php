<?php

namespace App\Tests\Utils\Saver;

use App\Entity\Search;
use App\Entity\TripArticle;
use App\Entity\TripPage;
use App\Exception\FalseException;
use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\PageAttraction\Model\Article;
use App\Utils\Crawler\PageAttraction\Model\Page as PageModel;
use App\Utils\Crawler\PageAttraction\PageAttractionInterface;

class PageAttractionTest extends ContainerKernelTestCase
{
    public function testSave(): void
    {
        $this->getEntityManager(true);

        $pageAttractionService = $this->createMock(PageAttractionInterface::class);
        $pageAttractionService
            ->expects(self::once())
            ->method('getPages')
            ->willReturn([
                new PageModel('#1', [new Article('title 1', ['description'], ['image']), new Article('title 2')], '#'),
                new PageModel('#2', [new Article('title 2'), new Article('title 2', ['description'], ['image'])]),
            ]);
        $pageAttractionService
            ->method('getSource')
            ->willReturn(__CLASS__);

        $this->getSaverPageAttraction()->save(
            $pageAttractionService,
            'Zakynthos',
            'Grecja',
            new Search()
        );

        /** @var TripPage[] $tripPageEntities */
        $tripPageEntities = $this->getEntityManager()->getFlushEntities();
        $firstArticle = $tripPageEntities[0]->getTripArticles()->first() ?: throw new FalseException();

        $this->assertCount(2, $tripPageEntities);
        $this->assertInstanceOf(TripPage::class, $tripPageEntities[0]);
        $this->assertSame('#', $tripPageEntities[0]->getMap());
        $this->assertSame('#1', $tripPageEntities[0]->getUrl());
        $this->assertCount(2, $tripPageEntities[0]->getTripArticles());
        $this->assertInstanceOf(TripArticle::class, $firstArticle);
        $this->assertSame('title 1', $firstArticle->getTitle());
        $this->assertSame(['description'], $firstArticle->getDescriptions());
        $this->assertSame(['image'], $firstArticle->getImages());
        $this->assertSame(__CLASS__, $tripPageEntities[0]->getSource());
    }
}
