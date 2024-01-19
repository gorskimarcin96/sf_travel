<?php

namespace App\Utils\Crawler\PageAttraction;

use App\Exception\NullException;
use App\Utils\Crawler\PageAttraction\Model\Article;
use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Crawler\PageAttraction\Model\PageAttractionOptions;
use App\Utils\Helper\Base64;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Symfony\Component\String\u;

final readonly class MamaSaidBeCool extends AbstractPageAttraction implements PageAttractionInterface
{
    public function __construct(
        HttpClientInterface $httpClient,
        Base64 $base64,
        LoggerInterface $downloaderLogger,
    ) {
        parent::__construct(
            $httpClient,
            $base64,
            $downloaderLogger,
            new PageAttractionOptions(
                'https://www.mamasaidbecool.pl/kategoria/',
                'h2>a',
                'div.entry-content>*',
            )
        );
    }

    #[\Override] public function getSource(): string
    {
        return self::class;
    }

    /** @return Page[] */
    #[\Override] public function getPages(string $place, string $nation): array
    {
        return $this->getPagesFromUrls($this->getUrls($place, $nation));
    }

    /** @return Page[] */
    #[\Override] protected function getPagesFromUrls(array $urls): array
    {
        $pages = array_map(function (string $url): Page {
            $this->downloaderLogger->info(sprintf('Download data from %s...', $url));
            $crawler = new Crawler($this->httpClient->request('GET', $url)->getContent());
            $page = new Page($url);
            $page->addArticle(new Article($crawler->filter('h1')->text()));

            $crawler->filter($this->pageAttractionOptions->getMainContentSelector())->each(function (Crawler $crawler) use ($page): void {
                if (
                    in_array($crawler->nodeName(), $this->pageAttractionOptions->getTitleSelectors())
                    && $crawler->text()
                ) {
                    $page->addArticle(new Article($crawler->text()));
                } elseif ('p' === $crawler->nodeName() && str_contains($crawler->text(), 'mapie')) {
                    $page->setMap($crawler->filter('a')->first()->attr('href'));
                } elseif (
                    in_array($crawler->nodeName(), $this->pageAttractionOptions->getImageSelectors())
                    && $crawler->filter('img')->count()
                ) {
                    $src = $crawler->filter('img')->eq(1)->attr('src') ?? throw new NullException();
                    $page->lastArticle()?->addImage($this->base64->convertFromImage($src));
                } elseif (
                    in_array($crawler->nodeName(), $this->pageAttractionOptions->getTextSelectors())
                    && u($crawler->text())->trim()->toString()
                ) {
                    $page->lastArticle()?->addDescription(u($crawler->text())->trim()->toString());
                }
            });

            return $page;
        }, $urls);

        $this->downloaderLogger->info(sprintf('Found pages: %s', count($pages)));

        return $pages;
    }
}
