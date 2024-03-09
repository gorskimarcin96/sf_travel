<?php

namespace App\Utils\Crawler\OptionalTrip;

use App\Exception\NullException;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip;
use App\Utils\Crawler\PantherClient;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use Facebook\WebDriver\WebDriverKeys;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler as PantherCrawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Itaka extends PantherClient implements OptionalTripInterface
{
    private const string MAIN_DOMAIN = 'https://itaka.seeplaces.com';
    private const string URL = 'https://itaka.seeplaces.com';

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $downloaderLogger,
        private Parser $parser,
        private Base64 $base64,
        Client $client,
    ) {
        parent::__construct($client);
    }

    #[\Override]
    public function getSource(): string
    {
        return self::class;
    }

    /** @return OptionalTrip[] */
    #[\Override]
    public function getOptionalTrips(string $place, string $nation): array
    {
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
            ->each(fn (PantherCrawler $pantherCrawler): PantherCrawler => $pantherCrawler);

        $nodes = array_filter($nodes, static fn (PantherCrawler $pantherCrawler): bool => str_contains($pantherCrawler->attr('href') ?? throw new NullException(), $place));
        $urls = array_map(static fn (PantherCrawler $pantherCrawler): string => self::URL.$pantherCrawler->attr('href'), $nodes);

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
            ->each(function (PantherCrawler $node): ?OptionalTrip {
                try {
                    return $this->createModelFromNode($node);
                } catch (\Throwable $exception) {
                    $this->downloaderLogger->error(sprintf('%s: %s', $exception::class, $exception->getMessage()));

                    return null;
                }
            });

        /** @var OptionalTrip[] $data */
        $data = array_filter($data, static fn (?OptionalTrip $optionalTrip): bool => $optionalTrip instanceof OptionalTrip);

        foreach ($data as $index => $datum) {
            $data[$index] = new OptionalTrip(
                $datum->getTitle(),
                $this->getDescriptions($datum->getUrl()),
                $datum->getUrl(),
                $datum->getImage(),
                $datum->getMoney()
            );
        }

        $this->downloaderLogger->info(sprintf('Found trips: %s', count($data)));

        return $data;
    }

    private function createModelFromNode(PantherCrawler $node): OptionalTrip
    {
        return new OptionalTrip(
            $node->filter('.excursion-tile__title')->text(),
            [],
            self::MAIN_DOMAIN.$node->filter('a')->getAttribute('href'),
            $this->base64->convertFromImage($node->filter('img')->getAttribute('src') ?? throw new NullException()),
            \App\Factory\Money::create($this->parsePrice($node->filter('.excursion-price-omnibus__value')->text()))
        );
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
                    ->each(fn (Crawler $crawler): string => $crawler->text())
            );
            $description[] = $crawler->filter('div.excursion-details__description--header')->text();
            $description[] = $crawler->filter('div.excursion-details__description--content')->text();
        } catch (\Throwable $exception) {
            $this->downloaderLogger->error(sprintf('%s: %s', $exception::class, $exception->getMessage()));
        }

        return $description;
    }
}
