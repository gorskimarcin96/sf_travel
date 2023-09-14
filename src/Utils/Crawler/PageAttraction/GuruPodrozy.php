<?php

namespace App\Utils\Crawler\PageAttraction;

use App\Exception\NationRequiredException;
use App\Utils\Crawler\PageAttraction\Model\Article;
use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Helper\Base64;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GuruPodrozy implements PageAttractionInterface
{
    private const PAGE = 'https://www.gurupodrozy.pl/wyprawy/europa/';

    public function __construct(private HttpClientInterface $httpClient, private Base64 $base64)
    {
    }

    /** @return Page[] */
    public function getPages(string $place, string $nation): array
    {
        $pages = [];
        $i = 1;

        while (true) {
            $crawler = new Crawler($this->httpClient->request('GET', self::PAGE.'/'.$nation.'/strona/'.$i++.'/')->getContent());
            $collection = new ArrayCollection($crawler->filter('h3>a')->each(fn (Crawler $node): string => $node->attr('href') ?? throw new NationRequiredException()));
            $urls = $collection->filter(fn (string $url) => str_contains($url, $place))->toArray();

            foreach ($urls as $url) {
                $page = new Page($url);
                $crawler = new Crawler($this->httpClient->request('GET', $url)->getContent());
                $crawler->filter('div.entry-content>*')->each(function (Crawler $node) use ($page) {
                    if ('h2' === $node->nodeName()) {
                        $page->addArticle(new Article($node->text()));
                    } elseif ($page->getArticles() && $node->filter('img')->count()) {
                        array_map(function (string $src) use ($page) {
                            if (!str_starts_with($src, 'data:image/svg')) {
                                $page->lastArticle()?->addImage($this->base64->convertFromImage($src));
                            }
                        }, $node->filter('img')->each(fn (Crawler $node): string => $node->attr('src') ?? throw new NationRequiredException()));
                    } elseif ($page->getArticles() && $node->text()) {
                        $page->lastArticle()?->addDescription($node->text());
                    }
                });
                $pages[] = $page;
            }

            if ($crawler->filter('section.recent-posts>article')->count() < 9) {
                break;
            }
        }

        return $pages;
    }

    public function getSource(): string
    {
        return self::class;
    }
}
