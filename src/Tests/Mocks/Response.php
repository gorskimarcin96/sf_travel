<?php

namespace App\Tests\Mocks;

use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

final readonly class Response implements ResponseInterface
{
    public function __construct(private string $content, private int $statusCode = HttpFoundationResponse::HTTP_OK)
    {
    }

    #[\Override]
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    #[\Override]
    public function getHeaders(bool $throw = true): array
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getContent(bool $throw = true): string
    {
        return $this->content;
    }

    /**
     * @return mixed[]
     */
    #[\Override]
    public function toArray(bool $throw = true): array
    {
        return json_decode($this->content, true, 512, JSON_THROW_ON_ERROR);
    }

    #[\Override]
    public function cancel(): void
    {
        throw new \LogicException('Method is not implemented.');
    }

    #[\Override]
    public function getInfo(string $type = null): mixed
    {
        throw new \LogicException('Method is not implemented.');
    }
}
