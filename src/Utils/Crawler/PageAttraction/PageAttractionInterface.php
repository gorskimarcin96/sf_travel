<?php

namespace App\Utils\Crawler\PageAttraction;

use App\Entity\SourceInterface;
use App\Utils\Crawler\PageAttraction\Model\Page;

interface PageAttractionInterface extends SourceInterface
{
    /** @return Page[] */
    public function getPages(string $place, string $nation): array;

    public function isEnabled(): bool;
}
