<?php

namespace App\Utils\Crawler\OptionalTrip;

use App\Entity\Money;
use App\Exception\NationRequiredException;
use App\Exception\NullException;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use Facebook\WebDriver\WebDriverKeys;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Itaka extends AbstractOptionalTrip implements OptionalTripInterface
{
    private const MAIN_DOMAIN = 'https://itaka.seeplaces.com';
    private const URL = 'https://itaka.seeplaces.com';

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $downloaderLogger,
        private Parser $parser,
        private Base64 $base64,
        Client $client,
    ) {
        parent::__construct($client);
    }

    /** @return OptionalTrip[] */
    public function getOptionalTrips(string $place, string $nation = null): array
    {
        if (!$nation) {
            throw new NationRequiredException();
        }

        $url = self::URL.'/pl/wycieczki/'.$nation.'/'.$place.'/';
        $pageIsExists = Response::HTTP_OK === $this->httpClient->request('GET', $url)->getStatusCode();

        if ($pageIsExists) {
            return $this->getByUrl($url);
        }

        $this->downloaderLogger->info(sprintf('Page %s is not exists. Searching pages by title...', $url));

        return $this->searchByTitle($nation, $place);
    }

    /** @return OptionalTrip[] */
    private function searchByTitle(string $nation, string $place): array
    {
        $this->client->request('GET', self::URL.'/pl/wycieczki/'.$nation.'/');
        $this->client->waitFor('.app-container');

        $nodes = $this->client
            ->getCrawler()
            ->filter('.app-container>div>section>section>div>div>a')
            ->each(fn (Crawler $node): Crawler => $node);

        $nodes = array_filter($nodes, static fn (Crawler $node) => str_contains($node->attr('href') ?? throw new NullException(), $place));
        $urls = array_map(static fn (Crawler $node) => self::URL.$node->attr('href'), $nodes);

        $this->downloaderLogger->info(sprintf('Found pages: %s', implode(',', $urls)));

        foreach ($urls as $url) {
            $data = array_merge($data ?? [], $this->getByUrl($url));
        }

        return $data ?? [];
    }

    /** @return OptionalTrip[] */
    private function getByUrl(string $url): array
    {
        $this->downloaderLogger->info(sprintf('Download data from %s...', $url));
        $this->client->request('GET', $url);
        $this->client->waitFor('.app-container');
        $this->client->getKeyboard()
            ->pressKey(WebDriverKeys::PAGE_DOWN)->pressKey(WebDriverKeys::PAGE_DOWN)
            ->pressKey(WebDriverKeys::PAGE_DOWN)->pressKey(WebDriverKeys::PAGE_DOWN);

        $data = $this->client
            ->getCrawler()
            ->filter('div.container>div>div.ant-col.region-excursions__item')
            ->each(function (Crawler $node) {
                try {
                    return new OptionalTrip(
                        $node->filter('.excursion-tile__title')->text(),
                        [],
                        self::MAIN_DOMAIN.$node->filter('a')->getAttribute('href'),
                        $this->base64->convertFromImage($node->filter('img')->getAttribute('src') ?? throw new NullException()),
                        (new Money())->setPrice($this->parsePrice($node->filter('.excursion-price-omnibus__value')->text()))
                    );
                } catch (\Throwable $exception) {
                    $this->downloaderLogger->error(sprintf('%s: %s', $exception::class, $exception->getMessage()));

                    return null;
                }
            });

        /** @var OptionalTrip[] $data */
        $data = array_filter($data, static fn (OptionalTrip|null $optionalTrip) => $optionalTrip instanceof OptionalTrip);

        foreach ($data as $index => $datum) {
            $data[$index] = new OptionalTrip(
                $datum->getTitle(),
                $this->getDescriptions($datum->getUrl()),
                $datum->getUrl(),
                $datum->getImg(),
                $datum->getMoney()
            );
        }

        $this->downloaderLogger->info(sprintf('Found trips: %s', count($data)));

        return $data;
    }

    public function getSource(): string
    {
        return self::class;
    }

    private function parsePrice(string $price): float
    {
        return $this->parser->stringToFloat(str_replace(',', '.', rtrim($price, '/os.')));
    }

    /** @return string[] */
    private function getDescriptions(string $url): array
    {
        $description = [];

        try {
            $crawler = $this->client->request('GET', $url);
            $description[] = $crawler->filter('div.excursion-details__highlights--header')->text();
            $description = array_merge(
                $description,
                $crawler->filter('div.excursion-details__highlights--content li')
                    ->each(fn (\Symfony\Component\DomCrawler\Crawler $node): string => $node->text())
            );
            $description[] = $crawler->filter('div.excursion-details__description--header')->text();
            $description[] = $crawler->filter('div.excursion-details__description--content')->text();
        } catch (\Throwable $exception) {
            $this->downloaderLogger->error(sprintf('%s: %s', $exception::class, $exception->getMessage()));
        }

        return $description;
    }
}
