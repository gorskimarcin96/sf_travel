<?php

namespace App\Utils\Crawler\OptionalTrip;

use App\Entity\Money;
use App\Exception\NationRequiredException;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\Client;

final readonly class Rainbow implements OptionalTripInterface
{
    private const URL = 'https://r.pl';

    public function __construct(
        private Client $client,
        private Parser $parser,
        private Base64 $base64
    ) {
    }

    /** @return OptionalTrip[] */
    public function getOptionalTrips(string $place, string $nation = null): array
    {
        if (!$nation) {
            throw new NationRequiredException();
        }

        $url = self::URL.'/wycieczki-fakultatywne/'.$nation.'/'.$place.'/';

        $this->client->request('GET', $url);
        $this->client->waitFor('#bloczkiHTMLID');

        $urls = $this->client
            ->getCrawler()
            ->filter('#bloczkiHTMLID>.szukaj-bloczki__bloczek-wrapper>a')
            ->each(fn (Crawler $node): string => self::URL.$node->attr('href'));

        return array_map(function (string $url) {
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
        }, $urls);
    }

    public function getSource(): string
    {
        return self::class;
    }
}
