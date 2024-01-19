<?php

namespace App\Utils\Crawler\Trip;

use App\Entity\Money;
use App\Exception\NullException;
use App\Utils\Crawler\Trip\Model\Trip;
use App\Utils\Enum\Food;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Wakacje implements TripInterface
{
    private const URL = 'https://www.wakacje.pl/wczasy';

    public function __construct(
        private HttpClientInterface $httpClient,
        private Parser $parser,
        private Base64 $base64,
        private LoggerInterface $downloaderLogger,
    ) {
    }

    public function getSource(): string
    {
        return self::class;
    }

    /**
     * @param Food[] $foods
     *
     * @return Trip[]
     */
    public function getTrips(
        string $place,
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        int $rangeFrom,
        int $rangeTo,
        array $foods,
        ?int $stars,
        ?float $rate,
        int $persons = 2
    ): array {
        $data = [];

        foreach (range(1, 2) as $page) {
            foreach ($this->getTripsFromPage(
                $place,
                $from,
                $to,
                $rangeFrom,
                $rangeTo,
                $foods,
                $stars,
                $rate,
                $persons,
                $page
            ) as $trip) {
                $data[] = $trip;
            }
        }

        return $data;
    }

    /**
     * @param Food[] $foods
     *
     * @return Trip[]
     */
    private function getTripsFromPage(
        string $place,
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        int $rangeFrom,
        int $rangeTo,
        array $foods,
        ?int $stars,
        ?float $rate,
        int $persons = 2,
        int $page = 1,
    ): array {
        $query = sprintf(
            'str-%s,od-%s,do-%s,%s-%s-dni,samolotem,%s,%s-gwiazdkowe,%sdorosle,ocena-%s,tanio,za-osobe&src=fromFilters',
            $page,
            $from->format('Y-m-d'),
            $to->format('Y-m-d'),
            $rangeFrom,
            $rangeTo,
            implode(',', array_map(static fn (Food $food): string => $food->value, $foods)),
            $stars ?? 1,
            $persons,
            $rate ?? 1,
        );
        $url = sprintf('%s/%s/?%s', self::URL, $place, $query);

        $this->downloaderLogger->info(sprintf('Download data from %s...', $url));

        $content = $this->httpClient->request('GET', $url)->getContent();
        $data = (new Crawler($content))
            ->filter('section[data-offers-count]>div>a')
            ->each(fn (Crawler $node): Trip => $this->createModelFromNode($node));

        $this->downloaderLogger->info(sprintf('Found trips: %s', count($data)));

        return $data;
    }

    private function createModelFromNode(Crawler $node): Trip
    {
        try {
            $rate = $this->parser->stringToFloat($node->filter('div[data-testid="RateBox"]')->text());
        } catch (\Throwable) {
            $rate = 0;
        }

        $dates = explode('- ', $node->filter('span[data-testid="offer-listing-duration-date"]')->first()->text());

        return new Trip(
            $node->filter('h4')->text(),
            $node->attr('href') ?? throw new NullException(),
            Food::fromValue($node->filter('span[data-testid="offer-listing-services"]')->text()),
            (int) $node->filter('div[data-testid="offer-listing-category"]')->attr('title'),
            $rate,
            $this->base64->convertFromImage($node->filter('picture>img')->attr('src') ?? throw new NullException()),
            new \DateTimeImmutable($dates[0]),
            new \DateTimeImmutable($dates[1]),
            (new Money())->setPrice($this->parser->stringToFloat($node->filter('div[data-testid="offer-listing-section-price"]')->text()))
        );
    }
}