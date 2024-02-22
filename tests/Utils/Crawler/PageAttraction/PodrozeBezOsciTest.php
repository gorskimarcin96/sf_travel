<?php

namespace App\Tests\Utils\Crawler\PageAttraction;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\PageAttraction\Model\Article;
use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Crawler\PageAttraction\PodrozeBezOsci;

class PodrozeBezOsciTest extends ContainerKernelTestCase
{
    public function testGetSource(): void
    {
        $this->assertSame(PodrozeBezOsci::class, $this->getPodrozeBezOsci()->getSource());
    }

    public function testGetPages(): void
    {
        $result = $this->getPodrozeBezOsci()->getPages('zakynthos', 'grecja');

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Page::class, $result[0]);
        $this->assertSame('test', $result[0]->getMap());
        $this->assertSame('https://podrozebezosci.pl/zakynthos', $result[0]->getUrl());
        $this->assertCount(1, $result[0]->getArticles());
        $this->assertInstanceOf(Article::class, $result[0]->getArticles()[0]);
        $this->assertSame('Title!', $result[0]->getArticles()[0]->getTitle());
        $this->assertSame(['Description'], $result[0]->getArticles()[0]->getDescriptions());
        $this->assertSame(['data:image/test;base64,'], $result[0]->getArticles()[0]->getImages());
    }
}
