<?php
require_once ROOT . '/Controllers/BaseController.php';


class AiController extends Controller
{
    private const OPENAI_ENDPOINT    = 'https://api.openai.com/v1/chat/completions';
    private const ANTHROPIC_ENDPOINT = 'https://api.anthropic.com/v1/messages';
    private const GEMINI_API_BASE    = 'https://generativelanguage.googleapis.com/v1beta/models/';
    private const LOG_FILE           = __DIR__ . '/../ai_debug.log';

    private function log(string $stage, $data = null): void
    {
        $line = '[' . date('Y-m-d H:i:s') . "] [$stage] ";
        if ($data !== null) {
            $line .= is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
        }
        @file_put_contents(self::LOG_FILE, $line . PHP_EOL, FILE_APPEND);
    }

    /**
     * Si ask() a renvoyé un message d’erreur (pas du JSON modèle), retourne le texte pour jsonOut['error'].
     */
    private function askFailureMessage(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }
        $prefixes = [
            '[Erreur OpenAI] ',
            '[Erreur OpenAI transport] ',
            '[Erreur Anthropic] ',
            '[Erreur Anthropic transport] ',
            '[Erreur Gemini] ',
            '[Erreur Gemini transport] ',
        ];
        foreach ($prefixes as $prefix) {
            if (strpos($raw, $prefix) === 0) {
                return trim(substr($raw, strlen($prefix)));
            }
        }
        if (strpos($raw, '[Aucune clé IA configurée') === 0) {
            return $raw;
        }
        return null;
    }

    /** POST /ai/summarize  body: text */
    public function summarize(): void
    {
        $this->log('summarize.entry', ['post_keys' => array_keys($_POST)]);
        $this->requireAuth();
        $text = trim($_POST['text'] ?? '');
        if ($text === '') { $this->log('summarize.empty'); $this->jsonOut(['error' => 'Texte vide.'], 400); }
        $prompt = "Résume le contenu suivant en 3 puces concises (français) :\n\n" . $text;
        $this->jsonOut(['result' => $this->ask($prompt)]);
    }

    /** POST /ai/generate  body: prompt */
    public function generate(): void
    {
        $this->log('generate.entry', ['post_keys' => array_keys($_POST)]);
        $this->requireAuth();
        $prompt = trim($_POST['prompt'] ?? '');
        if ($prompt === '') { $this->log('generate.empty'); $this->jsonOut(['error' => 'Prompt vide.'], 400); }
        $this->jsonOut(['result' => $this->ask($prompt)]);
    }

   
    public function rule(): void
    {
        $this->log('rule.entry', ['post_keys' => array_keys($_POST), 'topic' => $_POST['topic'] ?? null]);
        $this->requireAuth();
        $topic = trim($_POST['topic'] ?? '');
        if ($topic === '') { $this->log('rule.empty'); $this->jsonOut(['error' => 'Sujet vide.'], 400); }

        $prompt = "Génère une règle contractuelle professionnelle (français) sur le sujet : « {$topic} ».\n"
                . "Réponds UNIQUEMENT en JSON valide, sans markdown, avec exactement ces deux clés :\n"
                . "{\"titre\": \"...court, max 100 caractères...\", \"description\": \"...3-5 phrases claires et juridiquement précises...\"}";

        $raw = $this->ask($prompt);
        $this->log('rule.raw', $raw);
        if (($fail = $this->askFailureMessage($raw)) !== null) {
            $this->log('rule.ask_failure', $fail);
            $this->jsonOut(['error' => $fail], 502);
        }
        // Nettoyer les éventuels ```json ... ```
        $clean = trim(preg_replace('/^```(?:json)?|```$/m', '', $raw));
        $data  = json_decode($clean, true);
        $this->log('rule.parsed', ['json_err' => json_last_error_msg(), 'data' => $data]);

        if (!is_array($data) || !isset($data['titre'], $data['description'])) {
            $this->log('rule.invalid_response');
            $this->jsonOut(['error' => 'Réponse IA invalide.', 'raw' => $raw], 502);
        }
        $this->jsonOut([
            'titre'       => (string)$data['titre'],
            'description' => (string)$data['description'],
        ]);
    }

    /**
     * POST /ai/rules-batch
     * body: topic, count (1–10) — génère plusieurs règles contractuelles en une fois.
     */
    public function rulesBatch(): void
    {
        $this->log('rulesBatch.entry', ['post_keys' => array_keys($_POST)]);
        $this->requireAuth();
        $topic = trim($_POST['topic'] ?? '');
        $count = (int)($_POST['count'] ?? 3);
        $count = max(1, min(10, $count));
        if ($topic === '') {
            $this->log('rulesBatch.empty');
            $this->jsonOut(['error' => 'Sujet vide.'], 400);
        }

        $prompt = "Tu es juriste contractuel. Pour un contrat, génère exactement {$count} règles distinctes sur le thème : « {$topic} ».\n"
            . "Réponds UNIQUEMENT avec un tableau JSON valide (sans markdown, sans texte avant ou après), de {$count} objets, chacun avec exactement les clés \"titre\" et \"description\".\n"
            . "Titres courts (max 100 caractères). Descriptions : 2 à 4 phrases précises en français.\n"
            . "Exemple de forme : [{\"titre\":\"...\",\"description\":\"...\"},...]";

        $raw = $this->ask($prompt);
        $this->log('rulesBatch.raw', substr($raw, 0, 4000));
        if (($fail = $this->askFailureMessage($raw)) !== null) {
            $this->log('rulesBatch.ask_failure', $fail);
            $this->jsonOut(['error' => $fail], 502);
        }
        $clean = trim(preg_replace('/^```(?:json)?\s*|```$/m', '', $raw));
        $data  = json_decode($clean, true);
        $this->log('rulesBatch.parsed', ['json_err' => json_last_error_msg(), 'is_array' => is_array($data)]);

        if (!is_array($data)) {
            $this->jsonOut(['error' => 'Réponse IA invalide (JSON attendu).', 'raw' => $raw], 502);
        }
        if (isset($data['rules']) && is_array($data['rules'])) {
            $data = $data['rules'];
        }
        $rules = [];
        foreach ($data as $row) {
            if (!is_array($row) || !isset($row['titre'], $row['description'])) {
                continue;
            }
            $rules[] = [
                'titre'       => (string)$row['titre'],
                'description' => (string)$row['description'],
            ];
        }
        if ($rules === []) {
            $this->jsonOut(['error' => 'Aucune règle exploitable dans la réponse.', 'raw' => $raw], 502);
        }
        $this->jsonOut(['rules' => $rules]);
    }

    private function ask(string $prompt): string
    {
        $hasOpenai    = defined('OPENAI_API_KEY') && OPENAI_API_KEY !== '';
        $hasAnthropic = defined('ANTHROPIC_API_KEY') && ANTHROPIC_API_KEY !== '';
        $hasGemini    = defined('GEMINI_API_KEY') && GEMINI_API_KEY !== '';
        $provider     = defined('AI_PROVIDER') ? strtolower((string)AI_PROVIDER) : 'openai';

        $this->log('ask.dispatch', [
            'has_anthropic' => $hasAnthropic,
            'has_openai'    => $hasOpenai,
            'has_gemini'    => $hasGemini,
            'ai_provider'   => $provider,
            'prompt_len'    => strlen($prompt),
        ]);

        if ($provider === 'gemini') {
            if ($hasGemini) {
                return $this->askGemini($prompt);
            }
            if ($hasOpenai) {
                return $this->askOpenAi($prompt);
            }
            if ($hasAnthropic) {
                return $this->askAnthropic($prompt);
            }
        }
        if ($provider === 'anthropic') {
            if ($hasAnthropic) {
                return $this->askAnthropic($prompt);
            }
            if ($hasOpenai) {
                return $this->askOpenAi($prompt);
            }
            if ($hasGemini) {
                return $this->askGemini($prompt);
            }
        }
        // Défaut « openai » : OpenAI puis Claude puis Gemini.
        if ($hasOpenai) {
            return $this->askOpenAi($prompt);
        }
        if ($hasAnthropic) {
            return $this->askAnthropic($prompt);
        }
        if ($hasGemini) {
            return $this->askGemini($prompt);
        }
        $this->log('ask.no_key');
        return '[Aucune clé IA configurée : définir OPENAI_API_KEY, ANTHROPIC_API_KEY ou GEMINI_API_KEY]';
    }

    private function askAnthropic(string $prompt): string
    {
        $payload = json_encode([
            'model'      => ANTHROPIC_MODEL,
            'max_tokens' => 1024,
            'messages'   => [['role' => 'user', 'content' => $prompt]],
        ]);
        $ctx = stream_context_create(['http' => [
            'method'  => 'POST',
            'timeout' => 20,
            'header'  => "Content-Type: application/json\r\n"
                       . 'x-api-key: ' . ANTHROPIC_API_KEY . "\r\n"
                       . "anthropic-version: 2023-06-01\r\n",
            'content' => $payload,
            'ignore_errors' => true,
        ]]);
        $this->log('anthropic.request', ['endpoint' => self::ANTHROPIC_ENDPOINT, 'model' => ANTHROPIC_MODEL, 'payload_len' => strlen($payload)]);
        $resp = @file_get_contents(self::ANTHROPIC_ENDPOINT, false, $ctx);
        $status = $http_response_header[0] ?? 'no-headers';
        if ($resp === false) {
            $err = error_get_last();
            $this->log('anthropic.transport_error', ['status' => $status, 'php_err' => $err['message'] ?? null]);
            return '[Erreur Anthropic transport] ' . ($err['message'] ?? 'aucune réponse');
        }
        $this->log('anthropic.response', ['status' => $status, 'body' => substr($resp, 0, 2000)]);
        $j = json_decode($resp ?: '{}', true);
        return $j['content'][0]['text'] ?? ('[Erreur Anthropic] ' . ($j['error']['message'] ?? 'inconnue'));
    }

    private function askOpenAi(string $prompt): string
    {
        $payload = json_encode([
            'model'         => OPENAI_MODEL,
            'messages'      => [['role' => 'user', 'content' => $prompt]],
            'temperature'   => 0.4,
            'max_tokens'    => 4096,
        ]);
        $ctx = stream_context_create(['http' => [
            'method'  => 'POST',
            'timeout' => 45,
            'header'  => "Content-Type: application/json\r\n"
                       . 'Authorization: Bearer ' . OPENAI_API_KEY . "\r\n",
            'content' => $payload,
            'ignore_errors' => true,
        ]]);
        $this->log('openai.request', ['endpoint' => self::OPENAI_ENDPOINT, 'model' => OPENAI_MODEL, 'payload_len' => strlen($payload)]);
        $resp = @file_get_contents(self::OPENAI_ENDPOINT, false, $ctx);
        $status = $http_response_header[0] ?? 'no-headers';
        if ($resp === false) {
            $err = error_get_last();
            $this->log('openai.transport_error', ['status' => $status, 'php_err' => $err['message'] ?? null]);
            return '[Erreur OpenAI transport] ' . ($err['message'] ?? 'aucune réponse');
        }
        $this->log('openai.response', ['status' => $status, 'body' => substr($resp, 0, 2000)]);
        $j = json_decode($resp ?: '{}', true);
        return $j['choices'][0]['message']['content'] ?? ('[Erreur OpenAI] ' . ($j['error']['message'] ?? 'inconnue'));
    }

    private function askGemini(string $prompt): string
    {
        $model = defined('GEMINI_MODEL') ? (string)GEMINI_MODEL : 'gemini-flash-latest';
        // Clé en en-tête (recommandé), comme l’exemple officiel — pas dans l’URL (évite la clé dans les logs serveur).
        $url = self::GEMINI_API_BASE . rawurlencode($model) . ':generateContent';
        $payload = json_encode([
            'contents'         => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => [
                'temperature'     => 0.4,
                'maxOutputTokens' => 8192,
            ],
        ], JSON_UNESCAPED_UNICODE);
        $ctx = stream_context_create(['http' => [
            'method'        => 'POST',
            'timeout'       => 45,
            'header'        => "Content-Type: application/json\r\n"
                           . 'X-goog-api-key: ' . GEMINI_API_KEY . "\r\n",
            'content'       => $payload,
            'ignore_errors' => true,
        ]]);
        $this->log('gemini.request', ['url_model' => $model, 'payload_len' => strlen($payload)]);
        $resp = @file_get_contents($url, false, $ctx);
        $status = $http_response_header[0] ?? 'no-headers';
        if ($resp === false) {
            $err = error_get_last();
            $this->log('gemini.transport_error', ['status' => $status, 'php_err' => $err['message'] ?? null]);
            return '[Erreur Gemini transport] ' . ($err['message'] ?? 'aucune réponse');
        }
        $this->log('gemini.response', ['status' => $status, 'body' => substr($resp, 0, 2000)]);
        $j = json_decode($resp ?: '{}', true);
        if (!empty($j['error']['message'])) {
            return '[Erreur Gemini] ' . $j['error']['message'];
        }
        $parts = $j['candidates'][0]['content']['parts'] ?? [];
        $text  = $parts[0]['text'] ?? null;
        if ($text !== null && $text !== '') {
            return $text;
        }
        return '[Erreur Gemini] ' . ($j['promptFeedback']['blockReason'] ?? 'réponse vide ou filtrée');
    }

    private function jsonOut(array $data, int $status = 200): void
    {
        $this->log('jsonOut', ['status' => $status, 'data' => $data]);
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
