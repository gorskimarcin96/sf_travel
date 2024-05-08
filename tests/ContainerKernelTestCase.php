<?php

namespace App\Tests;

use App\Entity\City;
use App\Entity\LastMinute;
use App\Entity\Search;
use App\Entity\Weather;
use App\Factory\SearchServices;
use App\MessageHandler\LastMinuteHandler;
use App\MessageHandler\SearchHandler;
use App\Repository\CityRepositoryInterface;
use App\Repository\LastMinuteRepositoryInterface;
use App\Repository\SearchRepositoryInterface;
use App\Repository\WeatherRepositoryInterface;
use App\Tests\Mocks\BrowserManager;
use App\Tests\Mocks\EntityManager;
use App\Tests\Mocks\HttpClient;
use App\Tests\Mocks\Translation;
use App\Tests\Mocks\WebDriver;
use App\Utils\Api\Geocoding\OpenMeteo as GeocodingOpenMeteo;
use App\Utils\Api\Translation\DeepL as TranslationDeepL;
use App\Utils\Api\Translation\TranslationInterface;
use App\Utils\Api\Weather\OpenMeteo as WeatherOpenMeteo;
use App\Utils\Crawler\Flight\Booking as FlightBooking;
use App\Utils\Crawler\FlyCodes\Wikipedia as FlyCodesWikipedia;
use App\Utils\Crawler\Hotel\Booking as HotelBooking;
use App\Utils\Crawler\OptionalTrip\Itaka as OptionalTripItaka;
use App\Utils\Crawler\OptionalTrip\Rainbow as OptionalTripRainbow;
use App\Utils\Crawler\PageAttraction\BliskoCorazDalej;
use App\Utils\Crawler\PageAttraction\GuruPodrozy;
use App\Utils\Crawler\PageAttraction\MamaSaidBeCool;
use App\Utils\Crawler\PageAttraction\PodrozeBezOsci;
use App\Utils\Crawler\PageAttraction\TasteAway;
use App\Utils\Crawler\PageAttraction\Travelizer;
use App\Utils\Crawler\PageAttraction\TysiacStronSwiata;
use App\Utils\Crawler\Trip\Wakacje as TripWakacje;
use App\Utils\File\FileManager;
use App\Utils\Helper\Base64;
use App\Utils\Helper\Parser;
use App\Utils\Proxy\DatabaseWeather as ProxyDatabaseWeather;
use App\Utils\Saver\Flight as SaverFlight;
use App\Utils\Saver\Hotel as SaverHotel;
use App\Utils\Saver\OptionalTrip as SaverOptionalTrip;
use App\Utils\Saver\PageAttraction as SaverPageAttraction;
use App\Utils\Saver\Trip as SaverTrip;
use App\Utils\Saver\Weather as SaverWeather;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Panther\Client;

abstract class ContainerKernelTestCase extends KernelTestCase
{
    public function getSearchServices(): SearchServices
    {
        /** @var SearchServices $service */
        $service = self::getContainer()->get(SearchServices::class);

        return $service;
    }

    public function getFileManager(): FileManager
    {
        /** @var FileManager $service */
        $service = self::getContainer()->get(FileManager::class);

        return $service;
    }

    public function getHotelBooking(): HotelBooking
    {
        $httpClient = HttpClient::create()->addRequest(
            'GET',
            'https://www.booking.com/searchresults.pl.html?ss=Zakynthos&ssne=Zakynthos&ssne_untouched=Zakynthos&lang=pl&sb=1&src_elem=sb&dest_type=region&checkin=2000-01-01&checkout=2000-01-07&group_adults=2&no_rooms=1&group_children=1&order=price&search_selected=true&ac_suggestion_list_length=5&ac_langcode=en&ac_click_type=b&ac_position=0&src=searchresults&flex_window=0&nflt=ltfd=1:5:01-01-2000_07-01-2000:1:;nflt=review_score=80;nflt=class=4;nflt=class=5;mealplan:mealplan=4;mealplan:mealplan=3;mealplan:mealplan=9',
            $this->getFileManager()->read('var/test/hotel/booking/response.html')
        );

        return new HotelBooking($httpClient, new Base64(new FileManager(''), new Logger()), new Parser(), new Logger());
    }

    public function getFlightBooking(): FlightBooking
    {
        $webDriver = new WebDriver([
            'https://flights.booking.com/flights/WAW-ZTH/?type=ROUNDTRIP&cabinClass=ECONOMY&sort=BEST&travelPurpose=leisure&adults=2&children=0&from=WAW&to=ZTH&depart=2020-01-01&return=2020-01-07' => $this->getFileManager()->read('var/test/flight/booking/response.html'),
        ]);

        return new FlightBooking(new Client(new BrowserManager($webDriver)), new Parser(), new Logger());
    }

    public function getFlyCodesWikipedia(): FlyCodesWikipedia
    {
        $httpClient = HttpClient::create()->addRequest(
            'GET',
            'https://pl.wikipedia.org/wiki/Wikipedia:Skarbnica_Wikipedii/Porty_lotnicze_%C5%9Bwiata:_A',
            $this->getFileManager()->read('var/test/fly_codes/wikipedia/response.html')
        );

        return new FlyCodesWikipedia($httpClient, new Logger());
    }

    public function getGeocodingOpenMeteo(): GeocodingOpenMeteo
    {
        $httpClient = HttpClient::create()->addRequest(
            'GET',
            'https://geocoding-api.open-meteo.com/v1/search?name=Zakynthos&count=1&language=en&format=json',
            $this->getFileManager()->read('var/test/geocoding/open_meteo/response.json')
        );

        return new GeocodingOpenMeteo($httpClient);
    }

    public function getTranslationDeepL(): TranslationDeepL
    {
        $httpClient = HttpClient::create()->addRequest(
            'POST',
            'https://api-free.deepl.com/v2/translate',
            $this->getFileManager()->read('var/test/translation/deepl/response.json')
        );

        return new TranslationDeepL('', $httpClient);
    }

    public function getWeatherOpenMeteo(): WeatherOpenMeteo
    {
        $httpClient = HttpClient::create()->addRequest(
            'GET',
            'https://archive-api.open-meteo.com/v1/archive?latitude=37.78022&longitude=20.89555&start_date=2020-01-01&end_date=2020-01-07&daily=temperature_2m_mean%2Cprecipitation_hours%2Cprecipitation_sum',
            $this->getFileManager()->read('var/test/weather/open_meteo/response.json')
        );

        return new WeatherOpenMeteo($httpClient, $this->getGeocodingOpenMeteo());
    }

    public function getOptionalTripItaka(): OptionalTripItaka
    {
        $FM = $this->getFileManager();
        $url = 'https://itaka.seeplaces.com/pl/wycieczki/grecja/zakynthos/';
        $content = $FM->read('var/test/optional_trip/itaka/response.html');
        $subPage = 'https://itaka.seeplaces.com/pl/wycieczki/grecja/zakynthos/miasto-zakynthos-noca/';
        $searchPage = 'https://itaka.seeplaces.com/pl/wycieczki/grecja/';
        $searchSubPage = 'https://itaka.seeplaces.com/pl/wycieczki/grecja/kreta-chania/chania-noca/';
        $responses = [
            $url => $content,
            $subPage => '',
            $searchPage => $FM->read('var/test/optional_trip/itaka/response_3.html'),
            $searchSubPage => $FM->read('var/test/optional_trip/itaka/response_4.html'),
        ];

        return new OptionalTripItaka(
            HttpClient::create()->addRequest('GET', $url, $content),
            new Logger(),
            new Parser(),
            new Base64($FM, new Logger()),
            new Client(new BrowserManager(new WebDriver($responses)))
        );
    }

    public function getOptionalTripRainbow(): OptionalTripRainbow
    {
        $FM = $this->getFileManager();
        $url = 'https://r.pl/wycieczki-fakultatywne/grecja/zakynthos/';
        $content = $FM->read('var/test/optional_trip/rainbow/response.html');
        $subPageUrl = 'https://r.pl/wycieczki-fakultatywne/kierunki/grecja/zakynthos/magiczne-zante-468';
        $mainSearchPage = 'https://r.pl/wycieczki-fakultatywne/kierunki/grecja/';
        $searchPage = 'https://r.pl/wycieczki-fakultatywne/kierunki/grecja/kreta-chania';
        $searchSubPageUrl = 'https://r.pl/wycieczki-fakultatywne/kierunki/grecja/kreta-chania/knossos-i-heraklion-z-wizyta-u-minotaura-627';
        $responses = [
            $url => $content,
            $subPageUrl => $FM->read('var/test/optional_trip/rainbow/response_sub_page.html'),
            $mainSearchPage => $FM->read('var/test/optional_trip/rainbow/response_search.html'),
            $searchPage => $FM->read('var/test/optional_trip/rainbow/response_2.html'),
            $searchSubPageUrl => $FM->read('var/test/optional_trip/rainbow/response_sub_page_2.html'),
        ];

        return new OptionalTripRainbow(
            HttpClient::create()->addRequest('GET', $url, $content),
            new Logger(),
            new Parser(),
            new Base64(new Mocks\FileManager(), new Logger()),
            new Client(new BrowserManager(new WebDriver($responses)))
        );
    }

    public function getTripWakacje(): TripWakacje
    {
        $baseUrl = 'https://www.wakacje.pl/wczasy/zakynthos/?str-%s,od-2000-01-01,do-2000-01-06,5-7-dni,samolotem,2dorosle,tanio,za-osobe&src=fromFilters';
        $content = $this->getFileManager()->read('var/test/trip/wakacje/response.html');
        $httpClient = HttpClient::create()
            ->addRequest('GET', sprintf($baseUrl, 1), $content)
            ->addRequest('GET', sprintf($baseUrl, 2), $content);

        return new TripWakacje($httpClient, new Parser(), new Base64(new Mocks\FileManager(), new Logger()), new Logger(), new Logger());
    }

    public function getBliskoCorazDalej(): BliskoCorazDalej
    {
        return new BliskoCorazDalej(HttpClient::create(), new Base64(new Mocks\FileManager(), new Logger()), new Logger());
    }

    public function getGuruPodrozy(): GuruPodrozy
    {
        return new GuruPodrozy(HttpClient::create(), new Base64(new Mocks\FileManager(), new Logger()), new Logger());
    }

    public function getMamaSaidBeCool(): MamaSaidBeCool
    {
        $httpClient = HttpClient::create()->addRequest(
            'GET',
            'https://www.mamasaidbecool.pl/kategoria/grecja',
            $this->getFileManager()->read('var/test/page_attraction/mama_said_be_cool/response.html')
        )->addRequest(
            'GET',
            'https://www.mamasaidbecool.pl/zakynthos',
            $this->getFileManager()->read('var/test/page_attraction/mama_said_be_cool/response_2.html')
        );

        return new MamaSaidBeCool($httpClient, new Base64(new Mocks\FileManager(), new Logger()), new Logger(), new Logger());
    }

    public function getPodrozeBezOsci(): PodrozeBezOsci
    {
        $httpClient = HttpClient::create()->addRequest(
            'GET',
            'https://podrozebezosci.pl/europa/grecja',
            $this->getFileManager()->read('var/test/page_attraction/podrozebezosci/response.html')
        )->addRequest(
            'GET',
            'https://podrozebezosci.pl/zakynthos',
            $this->getFileManager()->read('var/test/page_attraction/podrozebezosci/response_2.html')
        );

        return new PodrozeBezOsci($httpClient, new Base64(new Mocks\FileManager(), new Logger()), new Logger());
    }

    public function getTasteAway(): TasteAway
    {
        return new TasteAway(HttpClient::create(), new Base64(new Mocks\FileManager(), new Logger()), new Logger());
    }

    public function getTravelizer(): Travelizer
    {
        return new Travelizer(HttpClient::create(), new Base64(new Mocks\FileManager(), new Logger()), new Logger());
    }

    public function getTysiacStronSwiata(): TysiacStronSwiata
    {
        return new TysiacStronSwiata(HttpClient::create(), new Base64(new Mocks\FileManager(), new Logger()), new Logger());
    }

    public function getSaverFlight(bool $simulatePhpWebDriverException = false): SaverFlight
    {
        return new SaverFlight(new Logger(), $this->getEntityManager(), $simulatePhpWebDriverException);
    }

    public function getSaverHotel(): SaverHotel
    {
        return new SaverHotel(new Logger(), $this->getEntityManager());
    }

    public function getSaverOptionalTrip(bool $simulatePhpWebDriverException = false): SaverOptionalTrip
    {
        return new SaverOptionalTrip(new Logger(), $this->getEntityManager(), $simulatePhpWebDriverException);
    }

    public function getSaverPageAttraction(): SaverPageAttraction
    {
        return new SaverPageAttraction(new Logger(), $this->getEntityManager());
    }

    public function getSaverTrip(): SaverTrip
    {
        return new SaverTrip(new Logger(), $this->getEntityManager());
    }

    public function getSaverWeather(): SaverWeather
    {
        return new SaverWeather($this->getProxyDatabaseWeather());
    }

    public function getProxyDatabaseWeather(): ProxyDatabaseWeather
    {
        return new ProxyDatabaseWeather(
            $this->getCityRepository(),
            $this->getWeatherRepository(),
            $this->getTranslation(),
            $this->getGeocodingOpenMeteo(),
            $this->getEntityManager(),
            new Logger()
        );
    }

    public function getMessageBus(): MessageBusInterface
    {
        /** @var MessageBusInterface $service */
        $service = self::getContainer()->get(MessageBusInterface::class);

        return $service;
    }

    /**
     * @param object[] $entities
     */
    public function getEntityManager(bool $reset = false, array $entities = []): EntityManager
    {
        /** @var EntityManager $service */
        $service = self::getContainer()->get(EntityManager::class);

        if ($reset) {
            $service->reset();
        }

        $service->loadEntities($entities);

        return $service;
    }

    public function getManagerRegistry(): ManagerRegistry
    {
        /** @var ManagerRegistry $service */
        $service = self::getContainer()->get(ManagerRegistry::class);

        return $service;
    }

    public function getSearchRepository(): SearchRepositoryInterface
    {
        return new Mocks\Repository\SearchRepository($this->getManagerRegistry(), Search::class, $this->getEntityManager());
    }

    public function getLastMinuteRepository(): LastMinuteRepositoryInterface
    {
        return new Mocks\Repository\LastMinuteRepository($this->getManagerRegistry(), LastMinute::class, $this->getEntityManager());
    }

    public function getCityRepository(): CityRepositoryInterface
    {
        return new Mocks\Repository\CityRepository($this->getManagerRegistry(), City::class);
    }

    public function getWeatherRepository(): WeatherRepositoryInterface
    {
        return new Mocks\Repository\WeatherRepository($this->getManagerRegistry(), Weather::class);
    }

    public function getSearchHandler(bool $simulatePhpWebDriverException = false): SearchHandler
    {
        return new SearchHandler(
            new Logger(),
            new Logger(),
            $this->getSearchServices(),
            $this->getSearchRepository(),
            $this->getMessageBus(),
            $this->getSaverOptionalTrip($simulatePhpWebDriverException),
            $this->getSaverPageAttraction(),
            $this->getSaverHotel(),
            $this->getSaverFlight($simulatePhpWebDriverException),
            $this->getSaverWeather(),
            $this->getSaverTrip(),
        );
    }

    public function getLastMinuteHandler(bool $simulatePhpWebDriverException = false): LastMinuteHandler
    {
        return new LastMinuteHandler(
            new Logger(),
            $this->getSearchServices(),
            $this->getLastMinuteRepository(),
            $this->getMessageBus(),
            $this->getSaverTrip(),
        );
    }

    private function getTranslation(): TranslationInterface
    {
        return new Translation();
    }
}
