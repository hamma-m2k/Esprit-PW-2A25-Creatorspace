<?php
/**
 * Translator — wrapper sur l'API LibreTranslate (gratuite, no-key par défaut).
 * 100% PHP natif via cURL, aucune dépendance externe.
 *
 * Endpoint public : https://libretranslate.com  (parfois rate-limited)
 * Self-hosted     : https://github.com/LibreTranslate/LibreTranslate
 *
 * Usage :
 *   $t = new Translator();
 *   $t->translate('Bonjour tout le monde', 'fr', 'en');  // → "Hello everyone"
 *   $t->detect('Hola amigo');                              // → "es"
 *
 * Configurable via constantes dans config/database.php :
 *   LIBRETRANSLATE_URL, LIBRETRANSLATE_KEY (optionnelle)
 */
class Translator {
    private string $endpoint;
    private ?string $apiKey;

    public function __construct(?string $endpoint = null, ?string $apiKey = null) {
        $this->endpoint = rtrim($endpoint ?? (defined('LIBRETRANSLATE_URL') ? LIBRETRANSLATE_URL : 'https://libretranslate.com'), '/');
        $this->apiKey   = $apiKey ?? (defined('LIBRETRANSLATE_KEY') ? LIBRETRANSLATE_KEY : null);
    }

    /**
     * Traduit un texte. Retourne le texte original en cas d'échec (failsafe).
     */
    public function translate(string $text, string $source, string $target): string {
        if (trim($text) === '' || $source === $target) return $text;

        // 1) Tentative LibreTranslate (si configuré et pas désactivé)
        $libre = $this->tryLibre($text, $source, $target);
        if ($libre !== null) return $libre;

        // 2) Fallback MyMemory — gratuit, pas de clé
        $mm = $this->tryMyMemory($text, $source, $target);
        if ($mm !== null) return $mm;

        return $text;
    }

    public function detect(string $text): ?string {
        if (trim($text) === '') return null;

        // LibreTranslate
        $payload = ['q' => $text];
        if ($this->apiKey) $payload['api_key'] = $this->apiKey;
        $resp = $this->post($this->endpoint . '/detect', $payload);
        if (is_array($resp) && isset($resp[0]['language'])) return $resp[0]['language'];
        if (isset($resp['language'])) return $resp['language'];

        // Fallback : on suppose 'fr' par défaut (le projet est francophone)
        return 'fr';
    }

    private function tryLibre(string $text, string $source, string $target): ?string {
        $payload = [
            'q' => $text, 'source' => $source, 'target' => $target, 'format' => 'text',
        ];
        if ($this->apiKey) $payload['api_key'] = $this->apiKey;
        $resp = $this->post($this->endpoint . '/translate', $payload);
        return $resp['translatedText'] ?? null;
    }

    private function tryMyMemory(string $text, string $source, string $target): ?string {
        $url = 'https://api.mymemory.translated.net/get?'
             . http_build_query(['q' => $text, 'langpair' => "$source|$target"]);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (!$raw || $code >= 400) return null;
        $data = json_decode($raw, true);
        return $data['responseData']['translatedText'] ?? null;
    }

    private function post(string $url, array $body): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($body),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$raw || $code >= 400) {
            // LibreTranslate public est devenu payant → 400 attendu, on tombe en fallback MyMemory.
            // On ne loggue qu'au-delà de 400 pour repérer les vraies pannes.
            if ($code !== 400) error_log("[Translator] HTTP $code on $url");
            return [];
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
}
