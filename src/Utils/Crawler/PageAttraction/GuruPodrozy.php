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

final readonly class GuruPodrozy implements PageAttractionInterface
{
    private const PAGE = 'https://www.gurupodrozy.pl/wyprawy/europa/';

    public function __construct(
        private HttpClientInterface $httpClient,
        private Base64 $base64,
        private LoggerInterface $downloaderLogger
    ) {
    }

    /** @return Page[] */
    public function getPages(string $place, string $nation): array
    {
        $pages = [];
        $i = 1;

        while (true) {
            $crawler = new Crawler($this->httpClient->request('GET', self::PAGE.'/'.$nation.'/strona/'.$i++.'/')->getContent());
            $collection = new ArrayCollection($crawler->filter('h3>a')->each(fn (Crawler $crawler): string => $crawler->attr('href') ?? throw new NationRequiredException()));
            $urls = $collection->filter(fn (string $url): bool => str_contains($url, $place))->toArray();

            foreach ($urls as $url) {
                $this->downloaderLogger->info(sprintf('Download data from %s...', $url));
                $page = new Page($url);
                $crawler = new Crawler($this->httpClient->request('GET', $url)->getContent());
                $crawler->filter('div.entry-content>*')->each(function (Crawler $crawler) use ($page): void {
                    if ('h2' === $crawler->nodeName()) {
                        $page->addArticle(new Article($crawler->text()));
                    } elseif ($page->getArticles() && $crawler->filter('img')->count()) {
                        array_map(function (string $src) use ($page): void {
                            if (!str_starts_with($src, 'data:image/svg')) {
                                $page->lastArticle()?->addImage($this->base64->convertFromImage($src));
                            }
                        }, $crawler->filter('img')->each(fn (Crawler $node): string => $crawler->attr('src') ?? throw new NationRequiredException()));
                    } elseif ($page->getArticles() && $crawler->text()) {
                        $page->lastArticle()?->addDescription($crawler->text());
                    }
                });
                $pages[] = $page;
            }

            if ($crawler->filter('section.recent-posts>article')->count() < 9) {
                break;
            }
        }

        $this->downloaderLogger->info(sprintf('Found pages: %s', count($pages)));

        return $pages;
    }

    public function getSource(): string
    {
        return self::class;
    }
}
