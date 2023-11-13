<?php

namespace App\Utils\Crawler\OptionalTrip;

use App\Entity\Money;
use App\Exception\NationRequiredException;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\WebDriverKeys;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\Client;

final readonly class Tui extends AbstractOptionalTrip implements OptionalTripInterface
{
    private const SLEEP_TIME = 7;
    private const MAIN_DOMAIN = 'https://www.tui.pl';
    private const URL = 'https://www.tui.pl/atrakcje/wyniki-wyszukiwania';

    public function __construct(
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
        $url = self::URL.'?'.http_build_query(['term' => $place]);

        $this->downloaderLogger->info(sprintf('Download data from %s...', $url));
        $this->client->request('GET', $url);
        try {
            $this->client->waitForElementToContain('button', 'Zaakceptuj', 5);
        } catch (TimeoutException) {
        }
        $this->client->executeScript("document.querySelectorAll('button')[3].click()");
        $this->client->waitForElementToContain('main div.border-blue-foamDark', 'Zobacz atrakcję');
        $this->client->waitForVisibility('main div.border-blue-foamDark img');
        $this->client->getKeyboard()
            ->pressKey(WebDriverKeys::PAGE_DOWN)->pressKey(WebDriverKeys::PAGE_DOWN)
            ->pressKey(WebDriverKeys::PAGE_DOWN)->pressKey(WebDriverKeys::PAGE_DOWN);

        sleep(self::SLEEP_TIME);

        $n = abs(ceil($this->parser->stringToFloat($this->client->getCrawler()->filter('h1')->text()) / 12));
        for ($i = 0; $i <= $n; ++$i) {
            if (str_contains($this->client->getCrawler()->text(), 'Pokaż więcej')) {
                $this->client->executeScript("Array.prototype.slice.call(document.getElementsByTagName('button')).filter(el => el.textContent.trim() === 'Pokaż więcej')[0].click()");
                sleep(self::SLEEP_TIME);
            } else {
                break;
            }

            0 === $i ?
                $this->client->getKeyboard()
                    ->pressKey(WebDriverKeys::PAGE_DOWN)->pressKey(WebDriverKeys::PAGE_DOWN)
                    ->pressKey(WebDriverKeys::PAGE_DOWN)->pressKey(WebDriverKeys::PAGE_DOWN)
                : $this->client->getKeyboard()->pressKey(WebDriverKeys::PAGE_DOWN)->pressKey(WebDriverKeys::PAGE_DOWN);
        }

        $data = $this->client
            ->getCrawler()
            ->filter('main div.border-blue-foamDark')
            ->each(function (Crawler $crawler): ?OptionalTrip {
                try {
                    return new OptionalTrip(
                        $crawler->filter('div.text-blue a')->first()->text(),
                        $crawler->filter('div.text-blue span')->first()->text(),
                        self::MAIN_DOMAIN.$crawler->filter('a')->first()->attr('href'),
                        $this->base64->convertFromImage($crawler->filter('img')->first()->attr('src') ?? throw new NationRequiredException()),
                        (new Money())->setPrice($this->parser->stringToFloat($crawler->filter('span.flex.items-baseline.font-headings')->text()) / 100)
                    );
                } catch (\Throwable $exception) {
                    $this->downloaderLogger->error(sprintf('%s: %s', $exception::class, $exception->getMessage()));

                    return null;
                }
            });

        /** @var OptionalTrip[] $data */
        $data = array_filter($data, static fn (OptionalTrip|null $optionalTrip): bool => $optionalTrip instanceof OptionalTrip);

        $this->downloaderLogger->info(sprintf('Found trips: %s', count($data)));

        return $data;
    }

    public function getSource(): string
    {
        return self::class;
    }
}
