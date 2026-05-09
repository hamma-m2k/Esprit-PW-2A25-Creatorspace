<?php
/**
 * BadwordsFilter — masquage d'insultes via API externe PurgoMalum.
 *
 * Stratégie :
 *  1) Appel à l'API PurgoMalum (EN, gratuite, sans clé)  → masque les mots anglais
 *  2) Application d'une liste FR locale en complément    → couvre le français
 *  3) Si l'API est injoignable (offline / timeout) → la liste locale FR+EN sert de fallback complet
 *
 * Endpoint : https://www.purgomalum.com/service/plain?text=...&fill_char=*
 *
 * Usage :
 *   $bw = new BadwordsFilter();
 *   $bw->clean('this is shit et merde');  // → "this is **** et *****"
 *   $bw->contains('hello');                // → false
 */
class BadwordsFilter {

    /** Liste FR (et fallback EN) — étendable via le constructeur. */
    private array $localWords = [
        // FR
        'con','connard','connasse','salope','salaud','enculé','encule','enculer',
        'merde','putain','pute','batard','bâtard','tg','ferme la',
        'nique','niquer','niqué','enfoiré','enfoire','débile','debile','idiot',
        // EN (fallback offline)
        'fuck','shit','bitch','asshole','dick','cunt','bastard','damn','crap',
        'motherfucker','retard','slut','whore',
    ];

    private bool $useApi;
    private string $apiUrl = 'https://www.purgomalum.com/service/plain';

    public function __construct(array $extraWords = [], bool $useApi = true) {
        if (!empty($extraWords)) {
            $this->localWords = array_merge($this->localWords, $extraWords);
        }
        $this->useApi = $useApi;
    }

    /** Vrai s'il existe au moins un badword (test local pour rapidité). */
    public function contains(string $text): bool {
        return !empty($this->found($text));
    }

    /** Liste des badwords FR/EN détectés localement. */
    public function found(string $text): array {
        $norm = $this->normalize($text);
        $hits = [];
        foreach ($this->localWords as $w) {
            $pattern = '/\b' . preg_quote($this->normalize($w), '/') . '\b/u';
            if (preg_match($pattern, $norm)) $hits[] = mb_strtolower($w);
        }
        return array_values(array_unique($hits));
    }

    /**
     * Nettoie le texte : appel API en priorité, fallback local en complément/secours.
     */
    public function clean(string $text, string $mask = '*'): string {
        if (trim($text) === '') return $text;

        $cleaned = $text;

        // 1) API PurgoMalum — best effort (couvre l'anglais)
        if ($this->useApi) {
            $remote = $this->callApi($cleaned, $mask);
            if ($remote !== null) {
                $cleaned = $remote;
            }
        }

        // 2) Liste FR locale en complément (PurgoMalum ne couvre pas le français)
        $cleaned = $this->cleanLocal($cleaned, $mask);

        return $cleaned;
    }

    /** Appel API PurgoMalum — retourne null en cas d'échec. */
    private function callApi(string $text, string $mask): ?string {
        $url = $this->apiUrl . '?' . http_build_query([
            'text'      => $text,
            'fill_char' => $mask,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT      => 'CreatorSpace/1.0',
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($resp === false || $code !== 200) {
            error_log("[BadwordsFilter] PurgoMalum HTTP $code : $err");
            return null;
        }
        return $resp;
    }

    /** Masque les mots de la liste locale (utilisé en complément FR + fallback). */
    private function cleanLocal(string $text, string $mask): string {
        $result = $text;
        foreach ($this->localWords as $w) {
            $pattern = '/\b' . preg_quote($w, '/') . '\b/iu';
            $result = preg_replace_callback(
                $pattern,
                fn($m) => str_repeat($mask, mb_strlen($m[0])),
                $result
            );
        }
        return $result;
    }

    /** Lowercase + sans accents pour matcher "merde", "MERDE", "mérde". */
    private function normalize(string $s): string {
        $s = mb_strtolower($s, 'UTF-8');
        if (function_exists('iconv')) {
            $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
            if ($t !== false) $s = $t;
        }
        return $s;
    }
}
