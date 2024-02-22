<?php

namespace App\Utils\Api\Translation;

use App\Utils\Api\Translation\Model\Translation;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class DeepL implements TranslationInterface
{
    private const string API_DOMAIN = 'https://api-free.deepl.com/v2/translate';

    public function __construct(private string $apiKey, private HttpClientInterface $client)
    {
    }

    /**
     * @return Translation[]
     */
    #[\Override]
    public function translate(string $text, string $targetLang, string $sourceLang = null): array
    {
        $data = $this->client->request('POST', self::API_DOMAIN, [
            'headers' => ['Authorization' => 'DeepL-Auth-Key '.$this->apiKey],
            'body' => ['text' => [$text], 'source_lang' => $sourceLang, 'target_lang' => $targetLang],
        ])->toArray();

        return array_map(static fn (array $row): Translation => new Translation($row['text']), $data['translations']);
    }
}
