<?php

namespace App\Tests\Utils\Crawler\OptionalTrip;

use App\Utils\Crawler\OptionalTrip\Itaka;
use App\Utils\Faker\Invoker;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\ProcessManager\BrowserManagerInterface;

class ItakaTest extends TestCase
{
    use Invoker;

    private Itaka $itaka;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itaka = new Itaka(new Client($this->createMock(BrowserManagerInterface::class)), new Parser(), new Base64());
    }

    /** @return string[][]|float[][] */
    public function getData(): array
    {
        return [
            ['od 97,23 zł /os.', 97.23],
            ['od 170,15 zł /os.', 170.15],
            ['od 194,46 zł /os.', 194.46],
        ];
    }

    /** @dataProvider getData */
    public function testParsePrice(string $input, float $expected): void
    {
        $this->assertSame($this->invokeMethod($this->itaka, 'parsePrice', [$input]), $expected);
    }
}
