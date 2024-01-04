<?php

namespace App\Utils\Api\Translation;

use App\Utils\Api\Translation\Model\Translation;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class DeepL
{
    private const API_DOMAIN = 'https://api-free.deepl.com/v2/translate';

    public function __construct(private string $apiKey, private HttpClientInterface $client)
    {
    }

    /**
     * @return Translation[]
     */
    public function translate(string $text, string $targetLang, string $sourceLang = null): array
    {
        $body = ['text' => $text, 'source_lang' => $sourceLang];

        if (null !== $sourceLang && '' !== $sourceLang) {
            $body['target_lang'] = $targetLang;
        }

        $data = $this->client->request('POST', self::API_DOMAIN, [
            'headers' => ['Authorization' => 'DeepL-Auth-Key '.$this->apiKey],
            'body' => $body,
        ])->toArray();

        return array_map(static fn (array $row): Translation => new Translation($row['text']), $data['translations']);
    }
}
