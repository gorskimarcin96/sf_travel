<?php

namespace App\Utils\Crawler\OptionalTrip;

use App\Entity\Money;
use App\Exception\NationRequiredException;
use App\Exception\NullException;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Panther\Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Rainbow extends AbstractOptionalTrip implements OptionalTripInterface
{
    private const URL = 'https://r.pl';

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
            ->each(fn (\Symfony\Component\Panther\DomCrawler\Crawler $node): Crawler => $node);

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
        $this->client->waitFor('#bloczkiHTMLID');

        $urls = $this->client
            ->getCrawler()
            ->filter('#bloczkiHTMLID>.szukaj-bloczki__bloczek-wrapper>a')
            ->each(fn (Crawler $node): string => self::URL.$node->attr('href'));

        $data = array_map(function (string $url) {
            try {
                $this->client->request('GET', $url);
                $this->client->refreshCrawler();
                $this->client->waitFor('.kf-opis-wycieczki-atrybut-podrzedny__opis>p');
                $crawler = new Crawler($this->client->getCrawler()->html());

                return new OptionalTrip(
                    $crawler->filter('h1')->text(),
                    $crawler->filter('.kf-opis-wycieczki-atrybut-podrzedny__opis>p')->each(fn (Crawler $node): string => $node->text()),
                    $url,
                    $this->base64->convertFromImage('https:'.$crawler->filter('img.kf-gallery--desktop__element')->attr('src')),
                    (new Money())->setPrice($this->parser->stringToFloat($crawler->filter('span.konfigurator__text--cena')->text()))
                );
            } catch (\Throwable $exception) {
                $this->downloaderLogger->error(sprintf('%s: %s', $exception::class, $exception->getMessage()));

                return null;
            }
        }, $urls);

        /** @var OptionalTrip[] $data */
        $data = array_filter($data, static fn (OptionalTrip|null $optionalTrip) => $optionalTrip instanceof OptionalTrip);

        $this->downloaderLogger->info(sprintf('Found trips: %s', count($data)));

        return $data;
    }

    public function getSource(): string
    {
        return self::class;
    }
}
