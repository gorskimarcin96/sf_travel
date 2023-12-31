<?php

namespace App\Utils\Crawler\Hotel;

use App\Entity\Money;
use App\Exception\NullException;
use App\Utils\Crawler\BookingHelper;
use App\Utils\Crawler\Hotel\Model\Hotel;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Booking implements HotelInterface
{
    use BookingHelper;
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
        $url = self::URL.'?'.implode('&', $params);

        $this->downloaderLogger->info(sprintf('Download data from %s...', $url));

        $crawler = new Crawler($this->httpClient->request('GET', $url)->getContent());

        $this->downloaderLogger->notice(sprintf('Got first %s hotels.', $crawler->filter($this->createAttr('property-card'))->count()));

        return $crawler->filter($this->createAttr('property-card'))->each(closure: function (Crawler $crawler) use ($betweenDays): Hotel {
            try {
                $text = $crawler->filter($this->createAttr('review-score', 'div', '>div'))->first()->text();
                $rate = $this->parser->stringToFloat(str_replace(',', '.', $text));
            } catch (\Throwable) {
                $rate = null;
            }

            $amount = $this->parser
                ->stringToFloat($crawler->filter($this->createAttr('price-and-discounted-price', 'span'))->text());

            try {
                $descriptionHeader = $crawler->filter($this->createAttr('recommended-units', 'div', ' h4'))->text();
            } catch (\Throwable) {
            }

            $descriptions = $crawler->filter($this->createAttr('recommended-units', 'div', ' ul>li'))
                ->each(fn (Crawler $node): string => $node->text());

            return new Model\Hotel(
                $crawler->filter('h3>a>div')->first()->text(),
                $crawler->filter('h3>a')->attr('href') ?? throw new NullException(),
                $this->base64->convertFromImage($crawler->filter('img')->attr('src') ?? throw new NullException()),
                $crawler->filter($this->createAttr('address', 'span'))->text(),
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
