<?php
namespace Model\Health;

/**
 * HealthModel – provides weather data for a given country.
 * Uses the free Open-Meteo API (no API key required).
 */
class HealthModel
{
    /**
     * Get weather forecast for a country (by name).
     * Returns array with temperature (C), weather description and icon URL.
     */
    public function getWeatherByCountry(string $country): array
    {
        // Simple mapping of country name to latitude/longitude – limited list for demo.
        $coords = $this->countryToCoordinates($country);
        if (!$coords) {
            return ['error' => "Pays introuvable"];
        }
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$coords['lat']}&longitude={$coords['lon']}&hourly=temperature_2m,weathercode&forecast_days=1";
        $response = @file_get_contents($url);
        if ($response === false) {
            return ['error' => 'Impossible de récupérer les données météo'];
        }
        $data = json_decode($response, true);
        if (!isset($data['hourly'])) {
            return ['error' => 'Réponse météo inattendue'];
        }
        // Take the first hour of forecast
        $temp = $data['hourly']['temperature_2m'][0] ?? null;
        $code = $data['hourly']['weathercode'][0] ?? null;
        $description = $this->weatherCodeToDescription($code);
        $icon = $this->weatherCodeToIcon($code);
        return [
            'temperature' => $temp,
            'description' => $description,
            'icon' => $icon,
        ];
    }

    private function countryToCoordinates(string $country): ?array
    {
        $map = [
            'france' => ['lat' => 48.8566, 'lon' => 2.3522],
            'germany' => ['lat' => 52.52, 'lon' => 13.405],
            'spain' => ['lat' => 40.4168, 'lon' => -3.7038],
            'italy' => ['lat' => 41.9028, 'lon' => 12.4964],
            'united states' => ['lat' => 38.9072, 'lon' => -77.0369],
        ];
        $key = strtolower(trim($country));
        return $map[$key] ?? null;
    }

    private function weatherCodeToDescription(?int $code): string
    {
        $descriptions = [
            0 => 'Clair',
            1 => 'Principalement clair',
            2 => 'Partiellement nuageux',
            3 => 'Couvert',
            45 => 'Brouillard',
            48 => 'Brouillard givrant',
            51 => 'Pluie légère',
            61 => 'Pluie',
            71 => 'Neige légère',
            80 => 'Averses',
            95 => 'Orage',
        ];
        return $descriptions[$code] ?? 'Données météo inconnues';
    }

    private function weatherCodeToIcon(?int $code): string
    {
        $icons = [
            0 => 'https://cdn-icons-png.flaticon.com/512/869/869869.png',
            1 => 'https://cdn-icons-png.flaticon.com/512/869/869869.png',
            2 => 'https://cdn-icons-png.flaticon.com/512/869/869870.png',
            3 => 'https://cdn-icons-png.flaticon.com/512/869/869871.png',
            45 => 'https://cdn-icons-png.flaticon.com/512/869/869872.png',
            48 => 'https://cdn-icons-png.flaticon.com/512/869/869872.png',
            51 => 'https://cdn-icons-png.flaticon.com/512/869/869873.png',
            61 => 'https://cdn-icons-png.flaticon.com/512/869/869873.png',
            80 => 'https://cdn-icons-png.flaticon.com/512/869/869874.png',
            95 => 'https://cdn-icons-png.flaticon.com/512/869/869875.png',
        ];
        return $icons[$code] ?? '';
    }
}
