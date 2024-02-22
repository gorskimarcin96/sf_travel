<?php

namespace App\Tests\Mocks;

use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class HttpClient implements HttpClientInterface
{
    /**
     * @var array<string, array<string, ResponseInterface>>
     */
    private array $requests = [];

    public static function create(): self
    {
        return new self();
    }

    public function addRequest(
        string $method,
        string $url,
        string $content,
        int $statusCode = HttpFoundationResponse::HTTP_OK
    ): self {
        $this->requests[$method][$url] = new Response($content, $statusCode);

        return $this;
    }

    /**
     * @param string[] $options
     */
    #[\Override]
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->requests[$method][$url] ?? new Response('', HttpFoundationResponse::HTTP_NOT_FOUND);
    }

    #[\Override]
    public function stream(
        iterable|ResponseInterface $responses,
        float $timeout = null
    ): ResponseStreamInterface {
        throw new \LogicException('Method is not implemented.');
    }

    /**
     * @param string[] $options
     */
    #[\Override]
    public function withOptions(array $options): static
    {
        throw new \LogicException('Method is not implemented.');
    }
}
