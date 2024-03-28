<?php

namespace App\Utils\Crawler\Hotel;

use App\Exception\NullException;
use App\Utils\Crawler\BookingHelper;
use App\Utils\Crawler\Common\Money;
use App\Utils\Crawler\Hotel\Model\Hotel;
use App\Utils\Enum\Food;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Booking implements HotelInterface
{
    use BookingHelper;

    public const string URL = 'https://www.booking.com/searchresults.pl.html';

    public function __construct(
        private HttpClientInterface $httpClient,
        private Base64 $base64,
        private Parser $parser,
        private LoggerInterface $downloaderLogger
    ) {
    }

    #[\Override]
    public function getSource(): string
    {
        return self::class;
    }

    /**
     * @param Food[] $foods
     *
     * @return Hotel[]
     */
    #[\Override]
    public function getHotels(
        string $place,
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        int $rangeFrom,
        int $rangeTo,
        array $foods = [],
        ?int $stars = null,
        ?float $rate = null,
        int $adults = 2,
        int $children = 0
    ): array {
        $nflt = [sprintf('ltfd=1:%s:%s_%s:1:', $rangeFrom, $from->format('d-m-Y'), $to->format('d-m-Y'))];

        if (null !== $rate && $rate <= 10) {
            $nflt[] = 'nflt=review_score='.($rate * 10);
        }

        if (null !== $stars && $stars >= 0 && $stars <= 5) {
            foreach (range($stars, 5) as $star) {
                $nflt[] = 'nflt=class='.$star;
            }
        }

        if (in_array(Food::ALL_INCLUSIVE, $foods, true)) {
            $nflt[] = 'mealplan:mealplan=4';
        }

        if (in_array(Food::BREAKFAST_LAUNCH_AND_DINNER, $foods, true)) {
            $nflt[] = 'mealplan:mealplan=3';
        }

        if (in_array(Food::BREAKFAST_AND_DINNER, $foods, true)) {
            $nflt[] = 'mealplan:mealplan=9';
        }

        if (in_array(Food::WITHOUT_FOOD, $foods, true)) {
            $nflt[] = 'mealplan:mealplan=999';
        }

        $params = [
            'ss' => $place,
            'ssne' => $place,
            'ssne_untouched' => $place,
            'lang' => 'pl',
            'sb' => '1',
            'src_elem' => 'sb',
            'dest_type' => 'region',
            'checkin' => $from->format('Y-m-d'),
            'checkout' => $to->format('Y-m-d'),
            'group_adults' => $adults,
            'no_rooms' => '1',
            'group_children' => $children,
            'order' => 'price',
            'search_selected' => 'true',
            'ac_suggestion_list_length' => '5',
            'ac_langcode' => 'en',
            'ac_click_type' => 'b',
            'ac_position' => '0',
            'src' => 'searchresults',
            'flex_window' => 0,
            'nflt' => implode(';', $nflt),
        ];
        $params = array_map(static fn ($key, $value): string => $key.'='.$value, array_keys($params), $params);
        $url = self::URL.'?'.implode('&', $params);

        $this->downloaderLogger->info(sprintf('Download data from %s...', $url));

        $crawler = new Crawler($this->httpClient->request('GET', $url)->getContent());

        $this->downloaderLogger
            ->notice(sprintf('Got first %s hotels.', $crawler->filter($this->createAttr('property-card'))->count()));

        return $crawler
            ->filter($this->createAttr('property-card'))
            ->each(closure: fn (Crawler $node): Hotel => $this->createModelFromNode($node, $from, $to));
    }

    private function createModelFromNode(Crawler $node, \DateTimeInterface $from, \DateTimeInterface $to): Hotel
    {
        try {
            $text = $node->filter($this->createAttr('review-score', 'div', '>div'))->first()->text();
            $rate = $this->parser->stringToFloat(str_replace(',', '.', $text));
        } catch (\Throwable) {
            $rate = null;
        }

        $betweenDays = explode(', ', $node->filter($this->createAttr('price-for-x-nights'))->first()->text())[0];
        $betweenDays = $this->parser->stringToFloat($betweenDays);

        $amount = $this->parser
            ->stringToFloat($node->filter($this->createAttr('price-and-discounted-price', 'span'))->text());

        try {
            $descriptionHeader = $node->filter($this->createAttr('recommended-units', 'div', ' h4'))->text();
        } catch (\Throwable) {
        }

        $descriptions = $node->filter($this->createAttr('recommended-units', 'div', ' ul>li'))
            ->each(fn (Crawler $node): string => $node->text());

        $food = match (true) {
            in_array('All inclusive', $descriptions, true) => Food::ALL_INCLUSIVE,
            in_array('Wszystkie posiłki wliczone w cenę', $descriptions, true) => Food::BREAKFAST_LAUNCH_AND_DINNER,
            in_array('Śniadanie i kolacja wliczone w cenę', $descriptions, true) => Food::BREAKFAST_AND_DINNER,
            in_array('Śniadanie wliczone w cenę', $descriptions, true) => Food::BREAKFAST,
            default => Food::WITHOUT_FOOD,
        };

        try {
            $now = new \DateTime();
            $dates = explode(' - ', $node->filter($this->createAttr('flexible-dates'))->first()->text());
            $from = new \DateTimeImmutable(substr($dates[0], 5).' '.$now->format('Y'));
            $to = new \DateTimeImmutable(substr($dates[1], 5).' '.$now->format('Y'));
        } catch (\Throwable) {
        }

        return new Hotel(
            $node->filter('h3>a>div')->first()->text(),
            $node->filter('h3>a')->attr('href') ?? throw new NullException(),
            $food,
            $node->filter($this->createAttr('rating-stars', 'div', '>span'))->count(),
            $rate,
            $this->base64->convertFromImage($node->filter('img')->attr('src') ?? throw new NullException()) ?? throw new NullException(),
            $node->filter($this->createAttr('address', 'span'))->text(),
            isset($descriptionHeader) ? [$descriptionHeader] + $descriptions : $descriptions,
            $from,
            $to,
            new Money($amount / $betweenDays),
        );
    }
}
