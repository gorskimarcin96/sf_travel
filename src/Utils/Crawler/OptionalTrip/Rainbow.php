<?php

namespace App\Utils\Crawler\OptionalTrip;

use App\Factory\Money;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip;
use App\Utils\Crawler\PantherClient;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler as PantherCrawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Rainbow extends PantherClient implements OptionalTripInterface
{
    private const string URL = 'https://r.pl';

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
        $url = self::URL.'/wycieczki-fakultatywne/'.$nation.'/'.$place.'/';
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
        $this->client->request('GET', self::URL.'/wycieczki-fakultatywne/kierunki/'.$nation.'/');
        $this->client->waitFor('#bloczkiHTMLID');

        $nodes = $this->client
            ->getCrawler()
            ->filter('a.szukaj-kierunek-card')
            ->each(fn (Crawler $crawler): PantherCrawler => $crawler);

        $nodes = array_filter($nodes, static fn (PantherCrawler $pantherCrawler): bool => str_contains(strtolower($pantherCrawler->text()), $place));
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
        $this->client->waitFor('#bloczkiHTMLID');

        $urls = $this->client
            ->getCrawler()
            ->filter('#bloczkiHTMLID>.szukaj-bloczki__bloczek-wrapper>a')
            ->each(fn (PantherCrawler $pantherCrawler): string => self::URL.$pantherCrawler->attr('href'));

        $data = array_map(function (string $url): ?OptionalTrip {
            try {
                $this->client->request('GET', $url);
                $this->client->refreshCrawler();
                $this->client->waitFor('.kf-opis-wycieczki-atrybut-podrzedny__opis>p');

                return $this->createModelFromNode(new PantherCrawler($this->client->getCrawler()->html()), $url);
            } catch (\Throwable $exception) {
                $this->downloaderLogger->error(sprintf('%s: %s', $exception::class, $exception->getMessage()));

                return null;
            }
        }, $urls);

        /** @var OptionalTrip[] $data */
        $data = array_filter($data, static fn (OptionalTrip|null $optionalTrip): bool => $optionalTrip instanceof OptionalTrip);

        $this->downloaderLogger->info(sprintf('Found trips: %s', count($data)));

        return $data;
    }

    private function createModelFromNode(PantherCrawler $node, string $url): OptionalTrip
    {
        return new OptionalTrip(
            $node->filter('h1')->text(),
            $node->filter('.kf-opis-wycieczki-atrybut-podrzedny__opis>p')->each(fn (PantherCrawler $node): string => $node->text()),
            $url,
            $this->base64->convertFromImage('https:'.$node->filter('img.kf-gallery--desktop__element')->attr('src')),
            Money::create($this->parser->stringToFloat($node->filter('span.konfigurator__text--cena')->text()))
        );
    }
}
