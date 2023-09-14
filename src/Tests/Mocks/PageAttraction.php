<?php

namespace App\Tests\Mocks;

use App\Utils\Crawler\PageAttraction\PageAttractionInterface;

final readonly class PageAttraction implements PageAttractionInterface
{
    /** @param \App\Utils\Crawler\PageAttraction\Model\Page[] $data */
    public function __construct(private array $data = [])
    {
    }

    /** @return \App\Utils\Crawler\PageAttraction\Model\Page[] */
    public function getPages(string $place, string $nation): array
    {
        return $this->data;
    }

    public function getSource(): string
    {
        return self::class;
    }
}
