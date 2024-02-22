<?php

namespace App\Utils\Proxy;

use App\Entity\City;
use App\Entity\Weather as EntityWeather;
use App\Repository\CityRepositoryInterface;
use App\Repository\WeatherRepositoryInterface;
use App\Utils\Api\Geocoding\OpenMeteo as GeocodingOpenMeteo;
use App\Utils\Api\Translation\TranslationInterface;
use App\Utils\Api\Weather\WeatherInterface;
use App\Utils\Helper\DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DatabaseWeather
{
    use DateTime;

    public function __construct(
        private CityRepositoryInterface $cityRepository,
        private WeatherRepositoryInterface $weatherRepository,
        private TranslationInterface $translation,
        private GeocodingOpenMeteo $geocodingOpenMeteo,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return EntityWeather[]
     */
    public function getByCityAndBetweenDate(
        WeatherInterface $weatherService,
        string $city,
        \DateTimeInterface $from,
        \DateTimeInterface $to
    ): array {
        if (!($entityCity = $this->cityRepository->findByNamePl($city)) instanceof City) {
            $cityEn = $this->translation->translate($city, 'en', 'pl')[0]->getText();
            $geocoding = $this->geocodingOpenMeteo->getByCity($cityEn);
            $entityCity = (new City())
                ->setNamePl($city)
                ->setNameEn($cityEn)
                ->setCountry($geocoding->getCountry())
                ->setCountryCode($geocoding->getCountryCode())
                ->setLatitude($geocoding->getLatitude())
                ->setLongitude($geocoding->getLongitude());

            $this->entityManager->persist($entityCity);
        }

        $arrayWeathers = $this->weatherRepository->findByCityAndBetweenDateAndSource($entityCity, $from, $to, $weatherService::class);
        $collectionWeather = new ArrayCollection($arrayWeathers);

        if ($collectionWeather->count() !== $this->countDaysBetween($from, $to)) {
            $serviceWeathers = $weatherService->getByCityAndBetweenDate($entityCity->getNameEn(), $from, $to);

            foreach ($serviceWeathers as $serviceWeather) {
                if (!$collectionWeather->exists(function (int $key, EntityWeather $entity) use ($serviceWeather): bool {
                    return $entity->getDate()->format('Y-m-d') === $serviceWeather->getDate()->format('Y-m-d');
                })) {
                    $entity = (new EntityWeather())
                        ->setCity($entityCity)
                        ->setDate($serviceWeather->getDate())
                        ->setTemperature2mMean($serviceWeather->getTemperature2mMean())
                        ->setPrecipitationSum($serviceWeather->getPrecipitationSum())
                        ->setPrecipitationHours($serviceWeather->getPrecipitationHours())
                        ->setSource($weatherService::class);

                    $this->entityManager->persist($entity);

                    $collectionWeather->add($entity);
                }
            }
        }

        $weathers = $collectionWeather->toArray();

        uasort($weathers, static function (EntityWeather $a, EntityWeather $b): int {
            return $a->getDate() <=> $b->getDate();
        });

        $this->entityManager->flush();

        return $weathers;
    }
}
