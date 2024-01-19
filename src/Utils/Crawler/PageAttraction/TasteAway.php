<?php

namespace App\Utils\Crawler\PageAttraction;

use App\Exception\NullException;
use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Crawler\PageAttraction\Model\PageAttractionOptions;
use App\Utils\Helper\Base64;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class TasteAway extends AbstractPageAttraction implements PageAttractionInterface
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
                'https://www.tasteaway.pl/category/podroze',
                'h3>a',
                'div.post-content>*',
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

    #[\Override] protected function getUrls(string $place, string $nation): array
    {
        $response = $this->httpClient->request('GET', sprintf('%s/%s', $this->pageAttractionOptions->getUrl(), $nation));
        $crawler = new Crawler($response->getContent());
        $urls = $crawler->filter($this->pageAttractionOptions->getMainPageSelector())->each(fn (Crawler $crawler): string => $crawler->attr('href') ?? throw new NullException());
        $i = 2;

        do {
            $response = $this->httpClient->request('GET', sprintf('%s/%s/page/%s/', $this->pageAttractionOptions->getUrl(), $nation, $i));

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                break;
            }

            $crawler = new Crawler($response->getContent());
            $newUrls = $crawler->filter($this->pageAttractionOptions->getMainPageSelector())->each(fn (Crawler $crawler): string => $crawler->attr('href') ?? throw new NullException());
            $urls = [...$urls, ...$newUrls];
        } while ($i++);

        return (new ArrayCollection($urls))->filter(fn (string $url): bool => str_contains($url, $place))->toArray();
    }
}
