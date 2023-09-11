<?php

namespace App\Utils\Crawler\OptionalTrip;

use App\Entity\Money;
use App\Exception\NationRequiredException;
use App\Exception\NullException;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use Facebook\WebDriver\WebDriverKeys;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\Client;

class Itaka implements OptionalTripInterface
{
    private const MAIN_DOMAIN = 'https://itaka.seeplaces.com';
    private const URL = 'https://itaka.seeplaces.com/pl/wycieczki';

    public function __construct(private readonly Client $client, private readonly Parser $parser, private readonly Base64 $base64)
    {
    }

    /** @return OptionalTrip[] */
    public function getOptionalTrips(string $place, string $nation = null): array
    {
        if (!$nation) {
            throw new NationRequiredException();
        }

        $this->client->request('GET', self::URL.'/'.$nation.'/'.$place.'/');
        $this->client->waitFor('.app-container');
        $this->client->getKeyboard()
            ->pressKey(WebDriverKeys::PAGE_DOWN)->pressKey(WebDriverKeys::PAGE_DOWN)
            ->pressKey(WebDriverKeys::PAGE_DOWN)->pressKey(WebDriverKeys::PAGE_DOWN);

        return $this->client
            ->getCrawler()
            ->filter('div.container>div>div.ant-col.region-excursions__item')
            ->each(fn (\Symfony\Component\Panther\DomCrawler\Crawler $node) => new OptionalTrip(
                $node->filter('.excursion-title')->text(),
                $node->filter('.region-excursion-item__highlights--list li')->each(fn (Crawler $node): string => $node->text()),
                self::MAIN_DOMAIN.$node->filter('a')->getAttribute('href'),
                $this->base64->convertFromImage($node->filter('img')->getAttribute('src') ?? throw new NullException()),
                (new Money())->setPrice($this->parser->stringToFloat($node->filter('.amount')->text()))->setCurrency($node->filter('.currency')->text())
            ));
    }

    public function getSource(): string
    {
        return self::class;
    }
}
