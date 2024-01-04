<?php

namespace App\Utils\Saver;

use App\Entity\Search;
use App\Utils\Api\Weather\WeatherInterface;
use App\Utils\Proxy\DatabaseWeather;

final readonly class Weather
{
    public function __construct(private DatabaseWeather $databaseWeather)
    {
    }

    public function save(WeatherInterface $weatherService, Search $search): Search
    {
        foreach (range(5, 1) as $y) {
            foreach ($this->databaseWeather->getByCityAndBetweenDate(
                $weatherService,
                $search->getPlace(),
                $search->getFrom()->modify(sprintf('-%s year', $y)),
                $search->getTo()->modify(sprintf('-%s year', $y))
            ) as $weather) {
                $search->addWeather($weather);
            }
        }

        return $search;
    }
}
