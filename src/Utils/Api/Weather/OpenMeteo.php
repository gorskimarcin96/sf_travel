<?php

namespace App\Utils\Api\Weather;

use App\Utils\Api\Geocoding\OpenMeteo as GeocodingOpenMeteo;
use App\Utils\Api\Weather\Model\Weather;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class OpenMeteo implements WeatherInterface
{
    public const string API_DOMAIN = 'https://archive-api.open-meteo.com/v1/archive';

    public function __construct(private HttpClientInterface $client, private GeocodingOpenMeteo $geocodingOpenMeteo)
    {
    }

    #[\Override]
    public function getSource(): string
    {
        return self::class;
    }

    /**
     * @return Weather[]
     */
    #[\Override]
    public function getByCityAndBetweenDate(
        string $city,
        \DateTimeInterface $from,
        \DateTimeInterface $to
    ): array {
        $geocoding = $this->geocodingOpenMeteo->getByCity($city);

        return $this->getByLatAndLongAndBetweenDate($geocoding->getLatitude(), $geocoding->getLongitude(), $from, $to);
    }

    /**
     * @return Weather[]
     */
    private function getByLatAndLongAndBetweenDate(
        float $latitude,
        float $longitude,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
    ): array {
        $weathers = [];
        $query = http_build_query([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'daily' => 'temperature_2m_mean,precipitation_hours,precipitation_sum',
        ]);
        $data = $this->client->request('GET', self::API_DOMAIN.'?'.$query)->toArray()['daily'];

        for ($i = 0; $i < count($data['time']) - 1; ++$i) {
            $weathers[] = new Weather(
                new \DateTime($data['time'][$i]),
                $data['temperature_2m_mean'][$i],
                $data['precipitation_hours'][$i],
                $data['precipitation_sum'][$i],
            );
        }

        return $weathers;
    }
}
