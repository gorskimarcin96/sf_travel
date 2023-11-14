<?php

namespace App\Utils\Crawler\FlyCodes;

use App\Exception\EmptyStringException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Symfony\Component\String\u;

final readonly class Wikipedia
{
    public const URL = 'https://pl.wikipedia.org/wiki/Wikipedia:Skarbnica_Wikipedii/Porty_lotnicze_%C5%9Bwiata:_';

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $downloaderLogger
    ) {
    }

    /**
     * @return \Generator<FlyCode>
     */
    public function getFlyCodes(): \Generator
    {
        foreach (range('A', 'Z') as $letter) {
            $this->downloaderLogger->info(sprintf('Get codes from: %s', self::URL.$letter));

            $crawler = new Crawler($this->httpClient->request('GET', self::URL.$letter)->getContent());

            $this->downloaderLogger->info(sprintf('Found %s codes.', $crawler->filter('div#mw-content-text>div>ul>li')->count()));

            foreach ($crawler->filter('div#mw-content-text>div>ul>li')->each(function (Crawler $crawler): FlyCode {
                $unicodeString = u($crawler->text())->ascii();
                $separator = substr($unicodeString->toString(), 3, 2);
                [$code, $unicodeString] = explode('' !== $separator ? $separator : throw new EmptyStringException(), $unicodeString->toString());
                $lineChunk = explode(',', $unicodeString);

                return new FlyCode(trim($code), trim($lineChunk[0]), trim($lineChunk[array_key_last($lineChunk)]));
            }) as $flyCode) {
                yield $flyCode;
            }
        }
    }
}
