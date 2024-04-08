<?php

namespace App\Utils\Crawler\PageAttraction;

use App\Exception\NullException;
use App\Utils\Crawler\PageAttraction\Model\Article;
use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Crawler\PageAttraction\Model\PageAttractionOptions;
use App\Utils\Helper\Base64;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Symfony\Component\String\u;

abstract readonly class AbstractPageAttraction
{
    public function __construct(
        protected HttpClientInterface $httpClient,
        protected Base64 $base64,
        protected LoggerInterface $downloaderLogger,
        protected PageAttractionOptions $pageAttractionOptions,
    ) {
    }

    /** @return Page[] */
    public function getPages(string $place, string $nation): array
    {
        return $this->getPagesFromUrls($this->getUrls($place, $nation));
    }

    /**
     * @param string[] $urls
     *
     * @return Page[]
     */
    protected function getPagesFromUrls(array $urls): array
    {
        $pages = array_map(function (string $url): Page {
            $this->downloaderLogger->info('Download data from', [$url]);
            $crawler = new Crawler($this->httpClient->request('GET', $url)->getContent());
            $page = new Page($url);

            if (0 !== $crawler->filter('h1')->count()) {
                $page->addArticle(new Article($crawler->filter('h1')->text()));
            }

            $crawler->filter($this->pageAttractionOptions->getMainContentSelector())->each(function (Crawler $crawler) use ($page): void {
                if (
                    in_array($crawler->nodeName(), $this->pageAttractionOptions->getTitleSelectors())
                    && $crawler->text()
                ) {
                    $page->addArticle(new Article($crawler->text()));
                } elseif (
                    in_array($crawler->nodeName(), $this->pageAttractionOptions->getImageSelectors())
                    && $crawler->filter('img')->count()
                ) {
                    $crawler->filter('img')->each(function (Crawler $node) use ($page): void {
                        $src = $node->attr('data-src-fg') ?? $node->attr('src');

                        if (null === $src) {
                            return;
                        }

                        if ('' === $src) {
                            return;
                        }

                        if (str_ends_with($src, '.svg')) {
                            return;
                        }

                        if (str_starts_with($src, 'data:image/svg')) {
                            return;
                        }

                        if (null !== $image = $this->base64->convertFromImage($src)) {
                            $page->lastArticle()?->addImage($image);
                        }
                    });
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

    /**
     * @return string[]
     */
    protected function getUrls(string $place, string $nation): array
    {
        $response = $this->httpClient->request('GET', sprintf('%s/%s', $this->pageAttractionOptions->getUrl(), $nation));

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            return [];
        }

        $crawler = new Crawler($response->getContent());
        $collection = new ArrayCollection($crawler->filter($this->pageAttractionOptions->getMainPageSelector())->each(fn (Crawler $crawler): string => $crawler->filter('a')->first()->attr('href') ?? throw new NullException()));

        return $collection->filter(fn (string $url): bool => str_contains($url, $place))->toArray();
    }

    public function isEnabled(): bool
    {
        return true;
    }
}
