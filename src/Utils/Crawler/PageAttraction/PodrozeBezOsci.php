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

final readonly class PodrozeBezOsci extends AbstractPageAttraction implements PageAttractionInterface
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
                'https://podrozebezosci.pl/europa',
                'h3>a',
                'div.entry-content>*',
            )
        );
    }

    #[\Override]
    public function getSource(): string
    {
        return self::class;
    }

    /** @return Page[] */
    #[\Override]
    public function getPages(string $place, string $nation): array
    {
        return $this->getPagesFromUrls($this->getUrls($place, $nation));
    }

    /**
     * @param string[] $urls
     *
     * @return Page[]
     */
    #[\Override]
    protected function getPagesFromUrls(array $urls): array
    {
        $pages = array_map(function (string $url): Page {
            $this->downloaderLogger->info('Download data from', [$url]);
            $page = new Page($url);
            $crawler = new Crawler($this->httpClient->request('GET', $url)->getContent());
            if (0 !== $crawler->filter('iframe')->count()) {
                $page->setMap($crawler->filter('iframe')->last()->attr('data-src-cmplz'));
            }

            $crawler->filter('div.entry-content>*')->each(function (Crawler $crawler) use ($page): void {
                if (in_array($crawler->nodeName(), $this->pageAttractionOptions->getTitleSelectors())) {
                    $page->addArticle(new Article($crawler->text()));
                } elseif (
                    in_array($crawler->nodeName(), $this->pageAttractionOptions->getImageSelectors())
                    && $crawler->filter('p img')->count()
                ) {
                    $src = $crawler->filter('p img')->first()->attr('src') ?? throw new NullException();

                    if (null !== $image = $this->base64->convertFromImage($src)) {
                        $page->lastArticle()?->addImage($image);
                    }
                } elseif (in_array($crawler->nodeName(), $this->pageAttractionOptions->getTextSelectors())) {
                    $page->lastArticle()?->addDescription($crawler->text());
                }
            });

            return $page;
        }, $urls);

        $this->downloaderLogger->info(sprintf('Found pages: %s', count($pages)));

        return $pages;
    }
}
