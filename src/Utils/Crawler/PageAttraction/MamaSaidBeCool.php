<?php

namespace App\Utils\Crawler\PageAttraction;

use App\Exception\NationRequiredException;
use App\Utils\Crawler\PageAttraction\Model\Article;
use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Helper\Base64;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class MamaSaidBeCool implements PageAttractionInterface
{
    private const PAGE = 'https://www.mamasaidbecool.pl/kategoria/';

    public function __construct(
        private HttpClientInterface $httpClient,
        private Base64 $base64,
        private LoggerInterface $downloaderLogger
    ) {
    }

    /** @return Page[] */
    public function getPages(string $place, string $nation): array
    {
        $crawler = new Crawler($this->httpClient->request('GET', self::PAGE.'/'.$nation)->getContent());
        $collection = new ArrayCollection($crawler->filter('h2>a')->each(fn (Crawler $crawler): string => $crawler->attr('href') ?? throw new NationRequiredException()));
        $urls = $collection->filter(fn (string $url): bool => str_contains($url, $place))->toArray();
        $pages = array_map(function (string $url): Page {
            $this->downloaderLogger->info(sprintf('Download data from %s...', $url));
            $page = new Page($url);
            $crawler = new Crawler($this->httpClient->request('GET', $url)->getContent());

            $crawler->filter('div.entry-content>*')->each(function (Crawler $crawler) use ($page): void {
                if ('h3' === $crawler->nodeName()) {
                    $page->addArticle(new Article($crawler->text()));
                } elseif ('p' === $crawler->nodeName() && str_contains($crawler->text(), 'mapie')) {
                    $page->setMap($crawler->filter('a')->first()->attr('href'));
                } elseif ($page->getArticles() && 'p' === $crawler->nodeName() && $crawler->text()) {
                    $page->lastArticle()?->addDescription($crawler->text());
                } elseif ($page->getArticles() && 'div' === $crawler->nodeName()) {
                    $page->lastArticle()?->addImage($this->base64->convertFromImage($crawler->filter('img')->eq(1)->attr('src') ?? throw new NationRequiredException()));
                }
            });

            return $page;
        }, $urls);

        $this->downloaderLogger->info(sprintf('Found pages: %s', count($pages)));

        return $pages;
    }

    public function getSource(): string
    {
        return self::class;
    }
}
