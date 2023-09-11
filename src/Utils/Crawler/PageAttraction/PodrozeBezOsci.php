<?php

namespace App\Utils\Crawler\PageAttraction;

use App\Exception\NullException;
use App\Utils\Crawler\PageAttraction\Model\Article;
use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Helper\Base64;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PodrozeBezOsci implements PageAttractionInterface
{
    private const PAGE = 'https://podrozebezosci.pl/europa';

    public function __construct(private readonly HttpClientInterface $httpClient, private readonly Base64 $base64)
    {
    }

    /** @return Page[] */
    public function getPages(string $place, string $nation): array
    {
        $crawler = new Crawler($this->httpClient->request('GET', self::PAGE.'/'.$nation)->getContent());
        $collection = new ArrayCollection($crawler->filter('h3>a')->each(fn (Crawler $node): string => $node->attr('href') ?? throw new NullException()));
        $urls = $collection->filter(fn (string $url) => str_contains($url, $place))->toArray();

        return array_map(function (string $url) {
            $page = new Page($url);
            $crawler = new Crawler($this->httpClient->request('GET', $url)->getContent());
            if (0 !== $crawler->filter('iframe')->count()) {
                $page->setMap($crawler->filter('iframe')->last()->attr('data-src-cmplz'));
            }

            $crawler->filter('div.entry-content>*')->each(function (Crawler $node) use ($page) {
                if ('h2' === $node->nodeName()) {
                    $page->addArticle(new Article($node->text()));
                } elseif ($page->getArticles() && 'p' === $node->nodeName() && $node->filter('p img')->count()) {
                    $page->lastArticle()?->addImage($this->base64->convertFromImage($node->filter('p img')->first()->attr('src') ?? throw new NullException()));
                } elseif ($page->getArticles() && 'p' === $node->nodeName() && $node->text()) {
                    $page->lastArticle()?->addDescription($node->text());
                }
            });

            return $page;
        }, $urls);
    }

    public function getSource(): string
    {
        return self::class;
    }
}
