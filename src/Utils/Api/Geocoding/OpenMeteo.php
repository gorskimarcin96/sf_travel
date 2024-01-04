<?php

namespace App\Utils\Api\Geocoding;

use App\Utils\Api\Geocoding\Model\Geocoding;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class OpenMeteo
{
    public const API_DOMAIN = 'https://geocoding-api.open-meteo.com/v1/search';

    public function __construct(private HttpClientInterface $client)
    {
    }

    public function getByCity(string $city): Geocoding
    {
        $query = http_build_query(['name' => $city, 'count' => 1, 'language' => 'en', 'format' => 'json']);
        $data = $this->client->request('GET', self::API_DOMAIN.'?'.$query)->toArray()['results'][0];

        return new Geocoding(
            $data['id'],
            $data['name'],
            $data['latitude'],
            $data['longitude'],
            $data['country_code'],
            $data['country']
        );
    }
}
