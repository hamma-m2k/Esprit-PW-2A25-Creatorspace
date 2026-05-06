<?php
require_once ROOT . '/Controllers/BaseController.php';

/**
 * WeatherController — API externe Open-Meteo (gratuite, sans clé).
 * Geocoding + météo courante pour la ville définie dans WEATHER_CITY.
 */
class WeatherController extends Controller
{
    private const GEO_URL  = 'https://geocoding-api.open-meteo.com/v1/search';
    private const FORE_URL = 'https://api.open-meteo.com/v1/forecast';

    /** Page HTML simple intégrée au layout. */
    public function current(): void
    {
        $data = $this->fetchWeather(WEATHER_CITY);
        $this->render('weather/index', [
            'pageTitle' => 'Météo',
            'pageSubtitle' => 'Open-Meteo · ' . WEATHER_CITY,
            'weather' => $data,
        ]);
    }

    /** Endpoint JSON pour appel AJAX. */
    public function apiJson(): void
    {
        $city = $_GET['city'] ?? WEATHER_CITY;
        $this->json($this->fetchWeather($city));
    }

    private function fetchWeather(string $city): array
    {
        $geo = $this->httpGet(self::GEO_URL . '?' . http_build_query([
            'name' => $city, 'count' => 1, 'language' => 'fr', 'format' => 'json',
        ]));
        $g = json_decode($geo, true);
        if (empty($g['results'][0])) {
            return ['error' => 'Ville introuvable: ' . $city];
        }
        $lat = $g['results'][0]['latitude'];
        $lon = $g['results'][0]['longitude'];
        $name = $g['results'][0]['name'] . ', ' . ($g['results'][0]['country'] ?? '');

        $fore = $this->httpGet(self::FORE_URL . '?' . http_build_query([
            'latitude' => $lat, 'longitude' => $lon,
            'current' => 'temperature_2m,wind_speed_10m,relative_humidity_2m,weather_code',
            'timezone' => 'auto',
        ]));
        $f = json_decode($fore, true);
        return [
            'location'    => $name,
            'temperature' => $f['current']['temperature_2m'] ?? null,
            'humidity'    => $f['current']['relative_humidity_2m'] ?? null,
            'wind'        => $f['current']['wind_speed_10m'] ?? null,
            'code'        => $f['current']['weather_code'] ?? null,
            'time'        => $f['current']['time'] ?? null,
        ];
    }

    private function httpGet(string $url): string
    {
        $ctx = stream_context_create([
            'http' => ['timeout' => 5, 'header' => "User-Agent: CreatorSpace\r\n"],
        ]);
        return @file_get_contents($url, false, $ctx) ?: '{}';
    }

}
