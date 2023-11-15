<?php

namespace App\Utils\Crawler\Flight;

use App\Entity\Money;
use App\Utils\Crawler\BookingHelper;
use App\Utils\Crawler\Flight\Model\Flight;
use App\Utils\Crawler\PantherClient;
use App\Utils\Helper\Parser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;

final readonly class Booking extends PantherClient implements FlightInterface
{
    use BookingHelper;

    public const URL = 'https://flights.booking.com/flights/%s-%s/';

    public function __construct(Client $client, private Parser $parser, private LoggerInterface $downloaderLogger)
    {
        parent::__construct($client);
    }

    public function getFlights(
        string $fromAirport,
        string $toAirport,
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        int $adults = 2,
        int $children = 0
    ): array {
        $params = [
            'type' => 'ROUNDTRIP',
            'cabinClass' => 'ECONOMY',
            'sort' => 'BEST',
            'travelPurpose' => 'leisure',
            'adults' => $adults,
            'children' => $children,
            'from' => $fromAirport,
            'to' => $toAirport,
            'depart' => $from->format('Y-m-d'),
            'return' => $to->format('Y-m-d'),
        ];
        $params = array_map(static fn ($key, $value): string => $key.'='.$value, array_keys($params), $params);
        $url = sprintf(self::URL, $fromAirport, $toAirport).'?'.implode('&', $params);

        $this->downloaderLogger->info(sprintf('Download data from %s...', $url));
        $this->client->request('GET', $url);
        $this->client->waitFor($this->createAttr('searchresults_card'));
        $this->downloaderLogger->notice(sprintf('Got %s flights.', $this->client->getCrawler()->filter($this->createAttr('searchresults_card'))->count()));

        return $this->client->getCrawler()
            ->filter($this->createAttr('searchresults_card'))
            ->each(fn (Crawler $crawler): Flight => new Flight(
                $fromAirport,
                $this->createDepartureDateTimeImmutable($crawler, '0'),
                $this->createDestinationDateTimeImmutable($crawler, '0'),
                (int) $this->parser->stringToFloat($crawler->filter($this->createAttr('flight_card_segment_stops_0'))->text()),
                $toAirport,
                $this->createDepartureDateTimeImmutable($crawler, '1'),
                $this->createDestinationDateTimeImmutable($crawler, '1'),
                (int) $this->parser->stringToFloat($crawler->filter($this->createAttr('flight_card_segment_stops_1'))->text()),
                (new Money())->setPrice($this->parser->stringToFloat(str_replace(',', '.', $crawler->filter($this->createAttr('flight_card_price_total_price'))->text()))),
                $this->client->getCurrentURL()
            ));
    }

    public function getSource(): string
    {
        return self::class;
    }

    private function createDepartureDateTimeImmutable(Crawler $crawler, string $number): \DateTimeImmutable
    {
        return new \DateTimeImmutable(
            sprintf(
                '%s %s',
                $crawler->filter($this->createAttr('flight_card_segment_departure_time_'.$number))->text(),
                $crawler->filter($this->createAttr('flight_card_segment_departure_date_'.$number))->text()
            )
        );
    }

    private function createDestinationDateTimeImmutable(Crawler $crawler, string $number): \DateTimeImmutable
    {
        return new \DateTimeImmutable(
            sprintf(
                '%s %s',
                $crawler->filter($this->createAttr('flight_card_segment_destination_time_'.$number))->text(),
                $crawler->filter($this->createAttr('flight_card_segment_destination_date_'.$number))->text()
            )
        );
    }
}
