<?php

namespace App\Utils\Crawler\PageAttraction;

use App\Exception\NullException;
use App\Utils\Crawler\PageAttraction\Model\Article;
use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Helper\Base64;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class PodrozeBezOsci implements PageAttractionInterface
{
    private const PAGE = 'https://podrozebezosci.pl/europa';

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
        $collection = new ArrayCollection($crawler->filter('h3>a')->each(fn (Crawler $crawler): string => $crawler->attr('href') ?? throw new NullException()));
        $urls = $collection->filter(fn (string $url): bool => str_contains($url, $place))->toArray();
        $pages = array_map(function (string $url): Page {
            $this->downloaderLogger->info(sprintf('Download data from %s...', $url));
            $page = new Page($url);
            $crawler = new Crawler($this->httpClient->request('GET', $url)->getContent());
            if (0 !== $crawler->filter('iframe')->count()) {
                $page->setMap($crawler->filter('iframe')->last()->attr('data-src-cmplz'));
            }

            $crawler->filter('div.entry-content>*')->each(function (Crawler $crawler) use ($page): void {
                if ('h2' === $crawler->nodeName()) {
                    $page->addArticle(new Article($crawler->text()));
                } elseif ($page->getArticles() && 'p' === $crawler->nodeName() && $crawler->filter('p img')->count()) {
                    $page->lastArticle()?->addImage($this->base64->convertFromImage($crawler->filter('p img')->first()->attr('src') ?? throw new NullException()));
                } elseif ($page->getArticles() && 'p' === $crawler->nodeName() && $crawler->text()) {
                    $page->lastArticle()?->addDescription($crawler->text());
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
