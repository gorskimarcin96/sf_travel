<?php

namespace App\Tests\Behat;

use App\Exception\BehatException;
use App\Exception\FalseException;
use App\Kernel;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Coduo\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApiContext implements Context
{
    private Response $lastResponse;

    public function __construct(private readonly Kernel $kernel)
    {
    }

    /**
     * @Given I send a :method request to :url
     *
     * @throws \Exception
     */
    public function iSendARequestTo(string $method, string $url, ?PyStringNode $pyStringNode = null): void
    {
        $request = Request::create($url, $method, [], [], [], [], $pyStringNode);
        'POST' === $method ?
            $request->headers->add(['Accept' => 'application/json', 'Content-Type' => 'application/json']) :
            $request->headers->add(['Accept' => 'application/json']);

        $this->lastResponse = $this->kernel->handle($request);
    }

    /**
     * @Given I get response :code status code
     *
     * @throws \Exception
     */
    public function theResponseCodeIs(int $code): void
    {
        Assert::assertSame($this->lastResponse->getStatusCode(), $code);
    }

    /**
     * @Given I get response :number elements
     *
     * @throws \Exception
     */
    public function theResponseNElements(int $number): void
    {
        $json = json_decode($this->lastResponse->getContent() ?: throw new FalseException(), null, 512, JSON_THROW_ON_ERROR);

        Assert::assertSame(is_countable($json) ? count($json) : 0, $number);
    }

    /**
     * @Given I get response body:
     *
     * @throws BehatException
     */
    public function iGetResponseBody(PyStringNode $pyStringNode): void
    {
        $phpMatcher = new PHPMatcher();
        $content = $this->lastResponse->getContent();

        if (!$phpMatcher->match($content, $pyStringNode->getRaw())) {
            throw new BehatException($phpMatcher->error().", $content does not match ".$pyStringNode->getRaw());
        }
    }
}
