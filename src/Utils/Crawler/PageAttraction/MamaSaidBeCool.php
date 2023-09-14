<?php

namespace App\Utils\Crawler\PageAttraction;

use App\Exception\NationRequiredException;
use App\Utils\Crawler\PageAttraction\Model\Article;
use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Helper\Base64;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class MamaSaidBeCool implements PageAttractionInterface
{
    private const PAGE = 'https://www.mamasaidbecool.pl/kategoria/';

    public function __construct(private HttpClientInterface $httpClient, private Base64 $base64)
    {
    }

    /** @return Page[] */
    public function getPages(string $place, string $nation): array
    {
        $crawler = new Crawler($this->httpClient->request('GET', self::PAGE.'/'.$nation)->getContent());
        $collection = new ArrayCollection($crawler->filter('h2>a')->each(fn (Crawler $node): string => $node->attr('href') ?? throw new NationRequiredException()));
        $urls = $collection->filter(fn (string $url) => str_contains($url, $place))->toArray();

        return array_map(function (string $url) {
            $page = new Page($url);
            $crawler = new Crawler($this->httpClient->request('GET', $url)->getContent());

            $crawler->filter('div.entry-content>*')->each(function (Crawler $node) use ($page) {
                if ('h3' === $node->nodeName()) {
                    $page->addArticle(new Article($node->text()));
                } elseif ('p' === $node->nodeName() && str_contains($node->text(), 'mapie')) {
                    $page->setMap($node->filter('a')->first()->attr('href'));
                } elseif ($page->getArticles() && 'p' === $node->nodeName() && $node->text()) {
                    $page->lastArticle()?->addDescription($node->text());
                } elseif ($page->getArticles() && 'div' === $node->nodeName()) {
                    $page->lastArticle()?->addImage($this->base64->convertFromImage($node->filter('img')->eq(1)->attr('src') ?? throw new NationRequiredException()));
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
