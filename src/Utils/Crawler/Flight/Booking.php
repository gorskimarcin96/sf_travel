<?php

namespace App\Utils\Crawler\Flight;

use App\Utils\Crawler\BookingHelper;
use App\Utils\Crawler\Common\Money;
use App\Utils\Crawler\Flight\Model\Flight;
use App\Utils\Crawler\PantherClient;
use App\Utils\Helper\DateTime;
use App\Utils\Helper\Parser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;

final readonly class Booking extends PantherClient implements FlightInterface
{
    use BookingHelper;
    use DateTime;

    public const string URL = 'https://flights.booking.com/flights/%s-%s/';

    public function __construct(Client $client, private Parser $parser, private LoggerInterface $downloaderLogger)
    {
        parent::__construct($client);
    }

    #[\Override]
    public function getSource(): string
    {
        return self::class;
    }

    #[\Override]
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

        $this->downloaderLogger->info('Download data from', [$url]);
        $this->client->request('GET', $url);
        $this->client->waitFor($this->createAttr('searchresults_card'));
        $this->downloaderLogger->notice(sprintf('Got %s flights.', $this->client->getCrawler()->filter($this->createAttr('searchresults_card'))->count()));

        return $this->client->getCrawler()
            ->filter($this->createAttr('searchresults_card'))
            ->each(fn (Crawler $node): Flight => $this->createModelFromNode($node, $fromAirport, $toAirport));
    }

    private function createModelFromNode(Crawler $node, string $fromAirport, string $toAirport): Flight
    {
        return new Flight(
            $fromAirport,
            $this->createDepartureDateTimeImmutable($node, '0'),
            $this->createDestinationDateTimeImmutable($node, '0'),
            (int) $this->parser->stringToFloat($node->filter($this->createAttr('flight_card_segment_stops_0'))->text()),
            $toAirport,
            $this->createDepartureDateTimeImmutable($node, '1'),
            $this->createDestinationDateTimeImmutable($node, '1'),
            (int) $this->parser->stringToFloat($node->filter($this->createAttr('flight_card_segment_stops_1'))->text()),
            new Money($this->parser->stringToFloat(str_replace(',', '.', $node->filter($this->createAttr('flight_card_price_total_price'))->text())), false),
            $this->client->getCurrentURL()
        );
    }

    private function createDepartureDateTimeImmutable(Crawler $node, string $number): \DateTimeImmutable
    {
        return $this->createDateTimeImmutableFromNodes(
            $node->filter($this->createAttr('flight_card_segment_departure_time_'.$number)),
            $node->filter($this->createAttr('flight_card_segment_departure_date_'.$number))
        );
    }

    private function createDestinationDateTimeImmutable(Crawler $node, string $number): \DateTimeImmutable
    {
        return $this->createDateTimeImmutableFromNodes(
            $node->filter($this->createAttr('flight_card_segment_destination_time_'.$number)),
            $node->filter($this->createAttr('flight_card_segment_destination_date_'.$number))
        );
    }

    private function createDateTimeImmutableFromNodes(Crawler $timeNode, Crawler $dateNode): \DateTimeImmutable
    {
        [$day, $month] = explode(' ', $dateNode->text());
        $month = $this->monthToNumber($month);

        return new \DateTimeImmutable(sprintf('%s %s-%s-%s', $timeNode->text(), $day, $month, date('Y')));
    }
}
