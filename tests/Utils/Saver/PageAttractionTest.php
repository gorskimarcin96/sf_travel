<?php

namespace App\Tests\Utils\Saver;

use App\Entity\Search;
use App\Tests\Mocks\PageAttraction as Mock;
use App\Utils\Crawler\PageAttraction\Model\Article;
use App\Utils\Crawler\PageAttraction\Model\Page as Model;
use App\Utils\Saver\PageAttraction as Saver;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PageAttractionTest extends KernelTestCase
{
    private Saver $service;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Saver $service */
        $service = self::getContainer()->get(Saver::class);
        $this->service = $service;
    }

    public function testSave(): void
    {
        $result = $this->service->save(
            new Mock([new Model('https://example.org', [new Article('title', ['description'], ['https://example.jpg'])])]),
            'test',
            'test',
            new Search()
        );

        $this->assertInstanceOf(Search::class, $result);

        $tripPage = $result->getTripPages()->first() ?: throw new EntityNotFoundException();
        $this->assertSame('https://example.org', $tripPage->getUrl());

        $tripArticle = $result->getTripPages()->first()->getTripArticles()->first() ?: throw new EntityNotFoundException();
        $this->assertSame('title', $tripArticle->getTitle());
        $this->assertSame(['description'], $tripArticle->getDescriptions());
        $this->assertSame(['https://example.jpg'], $tripArticle->getImages());
    }
}
