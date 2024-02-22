<?php

namespace App\Utils\Crawler\PageAttraction;

use App\Utils\Crawler\PageAttraction\Model\PageAttractionOptions;
use App\Utils\Helper\Base64;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class BliskoCorazDalej extends AbstractPageAttraction implements PageAttractionInterface
{
    public function __construct(
        HttpClientInterface $httpClient,
        Base64 $base64,
        LoggerInterface $downloaderLogger,
    ) {
        parent::__construct(
            $httpClient,
            $base64,
            $downloaderLogger,
            new PageAttractionOptions(
                'https://bliskocorazdalej.pl/kategoria/blisko',
                'article.article-blog',
                'div.blog-content>*',
            )
        );
    }

    #[\Override]
    public function getSource(): string
    {
        return self::class;
    }
}
