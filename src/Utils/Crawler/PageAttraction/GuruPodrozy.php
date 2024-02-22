<?php

namespace App\Utils\Crawler\PageAttraction;

use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Crawler\PageAttraction\Model\PageAttractionOptions;
use App\Utils\Helper\Base64;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GuruPodrozy extends AbstractPageAttraction implements PageAttractionInterface
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
                'https://www.gurupodrozy.pl/wyprawy/europa',
                'section.recent-posts>article.post>section.entry-body',
                'div.entry-content>*',
                ['h1', 'h2', 'h3', 'h4', 'h5'],
                ['p', 'div', 'noscript'],
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

    #[\Override]
    protected function getUrls(string $place, string $nation): array
    {
        $urls = [];
        $i = 0;

        do {
            $crawler = new Crawler($this->httpClient->request('GET', sprintf('%s/%s/strona/%d/', $this->pageAttractionOptions->getUrl(), $nation, ++$i))->getContent());
            $newUrls = $crawler->filter($this->pageAttractionOptions->getMainPageSelector())->each(function (Crawler $crawler) use ($place): ?string {
                return str_contains(strtolower($crawler->text()), strtolower($place)) ? $crawler->filter('h3>a')->first()->attr('href') : null;
            });
            $urls = [...$urls, ...(new ArrayCollection($newUrls))->filter(fn (?string $url): bool => null !== $url)->toArray()];
        } while ($crawler->filter('section.recent-posts>article')->count() >= 9);

        return $urls;
    }
}
