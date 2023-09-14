<?php

namespace App\Utils\Crawler\OptionalTrip;

use App\Entity\Money;
use App\Exception\NationRequiredException;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use Facebook\WebDriver\WebDriverKeys;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\Client;

final readonly class Tui implements OptionalTripInterface
{
    private const MAIN_DOMAIN = 'https://www.tui.pl';
    private const URL = 'https://www.tui.pl/atrakcje/wyniki-wyszukiwania';

    public function __construct(
        private Client $client,
        private Parser $parser,
        private Base64 $base64,
        private LoggerInterface $logger
    ) {
    }

    /** @return OptionalTrip[] */
    public function getOptionalTrips(string $place, string $nation = null): array
    {
        $this->client->request('GET', self::URL.'?'.http_build_query(['term' => $place]));
        $this->client->waitForElementToContain('button', 'Zaakceptuj');
        $this->client->executeScript("document.querySelector('button').click()");
        $this->client->waitForElementToContain('main div.border-blue-foamDark', 'Zobacz atrakcję');
        $this->client->waitForVisibility('main div.border-blue-foamDark img');
        $this->client->getKeyboard()
            ->pressKey(WebDriverKeys::PAGE_DOWN)->pressKey(WebDriverKeys::PAGE_DOWN)
            ->pressKey(WebDriverKeys::PAGE_DOWN)->pressKey(WebDriverKeys::PAGE_DOWN);

        sleep(2);

        $n = abs(ceil($this->parser->stringToFloat($this->client->getCrawler()->filter('h1')->text()) / 12));
        for ($i = 0; $i <= $n; ++$i) {
            try {
                $this->client->executeScript("Array.prototype.slice.call(document.getElementsByTagName('button')).filter(el => el.textContent.trim() === 'Pokaż więcej')[0].click()");
            } catch (\Throwable $e) {
                $this->logger->warning($e::class.' '.$e->getMessage());
                break;
            }

            $this->client->getKeyboard()->pressKey(WebDriverKeys::PAGE_DOWN);
            sleep(3);
        }

        return $this->client
            ->getCrawler()
            ->filter('main div.border-blue-foamDark')
            ->each(fn (Crawler $node) => new OptionalTrip(
                $node->filter('div.text-blue a')->first()->text(),
                $node->filter('div.text-blue span')->first()->text(),
                self::MAIN_DOMAIN.$node->filter('a')->first()->attr('href'),
                $this->base64->convertFromImage($node->filter('img')->first()->attr('src') ?? throw new NationRequiredException()),
                (new Money())->setPrice($this->parser->stringToFloat($node->filter('span.flex.items-baseline.font-headings')->text()) / 100)
            ));
    }

    public function getSource(): string
    {
        return self::class;
    }
}
