<?php

namespace App\Tests\Utils\Helper;

use App\Utils\Helper\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /** @return string[][]|float[][] */
    public function getData(): array
    {
        return [
            ['text 123,456.789 text', 123456.789],
        ];
    }

    /** @dataProvider getData */
    public function testStringToFloat(string $input, float $expected): void
    {
        $parser = new Parser();

        $this->assertSame($parser->stringToFloat($input), $expected);
    }
}
