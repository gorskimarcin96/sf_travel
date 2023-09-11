<?php

namespace App\Utils\Crawler\PageAttraction;

use App\Utils\Crawler\PageAttraction\Model\Page;
use App\Utils\Crawler\SourceInterface;

interface PageAttractionInterface extends SourceInterface
{
    /** @return Page[] */
    public function getPages(string $place, string $nation): array;
}
