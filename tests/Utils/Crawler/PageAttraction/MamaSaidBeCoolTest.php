<?php

namespace App\Tests\Utils\Crawler\PageAttraction;

use App\Tests\ContainerKernelTestCase;
use App\Utils\Crawler\PageAttraction\MamaSaidBeCool;
use App\Utils\Crawler\PageAttraction\Model\Article;
use App\Utils\Crawler\PageAttraction\Model\Page;

class MamaSaidBeCoolTest extends ContainerKernelTestCase
{
    public function testGetSource(): void
    {
        $this->assertSame(MamaSaidBeCool::class, $this->getMamaSaidBeCool()->getSource());
    }

    public function testGetPages(): void
    {
        $result = $this->getMamaSaidBeCool()->getPages('zakynthos', 'grecja');

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Page::class, $result[0]);
        $this->assertSame('http://example.test', $result[0]->getMap());
        $this->assertSame('https://www.mamasaidbecool.pl/zakynthos', $result[0]->getUrl());
        $this->assertCount(3, $result[0]->getArticles());
        $this->assertInstanceOf(Article::class, $result[0]->getArticles()[0]);
        $this->assertSame('Zakynthos', $result[0]->getArticles()[0]->getTitle());
        $this->assertSame([], $result[0]->getArticles()[0]->getDescriptions());
        $this->assertSame([], $result[0]->getArticles()[0]->getImages());
    }
}
