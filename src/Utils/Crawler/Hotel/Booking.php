<?php

namespace App\Utils\Crawler\Hotel;

use App\Entity\Money;
use App\Exception\NullException;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Booking implements HotelInterface
{
    public const URL = 'https://www.booking.com/searchresults.pl.html';

    public function __construct(
        private HttpClientInterface $httpClient,
        private Base64 $base64,
        private Parser $parser,
        private LoggerInterface $downloaderLogger
    ) {
    }

    public function getHotels(string $place, \DateTimeImmutable $from, \DateTimeImmutable $to, int $adults = 2, int $children = 0): array
    {
        $betweenDays = abs($from->diff($to)->format('%a'));
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
            'nflt' => 'ht_id%3D204',
        ];
        $params = array_map(static fn ($key, $value): string => $key.'='.$value, array_keys($params), $params);
        $crawler = new Crawler($this->httpClient->request('GET', self::URL.'?'.implode('&', $params))->getContent());

        $this->downloaderLogger->notice(sprintf('Got first %s hotels.', $crawler->filter('div[data-testid="property-card"]')->count()));

        return $crawler->filter('div[data-testid="property-card"]')->each(function (Crawler $node) use ($betweenDays) {
            try {
                $text = $node->filter('div[data-testid="review-score"]>div')->first()->text();
                $rate = $this->parser->stringToFloat(str_replace(',', '.', $text));
            } catch (\Throwable) {
                $rate = null;
            }

            $amount = $this->parser
                ->stringToFloat($node->filter('span[data-testid="price-and-discounted-price"]')->text());

            try {
                $descriptionHeader = $node->filter('div[data-testid="recommended-units"] h4')->text();
            } catch (\Throwable) {
            }

            $descriptions = $node->filter('div[data-testid="recommended-units"] ul>li')
                ->each(fn (Crawler $node): string => $node->text());

            return new Model\Hotel(
                $node->filter('h3>a>div')->first()->text(),
                $node->filter('h3>a')->attr('href') ?? throw new NullException(),
                $this->base64->convertFromImage($node->filter('img')->attr('src') ?? throw new NullException()),
                $node->filter('span[data-testid="address"]')->text(),
                isset($descriptionHeader) ? [$descriptionHeader] + $descriptions : $descriptions,
                (new Money())->setPrice($amount / $betweenDays),
                $rate
            );
        });
    }

    public function getSource(): string
    {
        return self::class;
    }
}
