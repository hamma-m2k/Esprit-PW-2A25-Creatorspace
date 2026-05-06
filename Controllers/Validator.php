<?php
/**
 * Validator — déclaration de règles, exécution sur un tableau de données.
 *
 * Règles supportées :
 *   required, string, integer, numeric, email, url,
 *   min:N, max:N           (longueur string OU valeur numeric)
 *   between:A,B
 *   in:val1,val2,...       (liste blanche)
 *   regex:/pattern/        (pattern PCRE)
 *   confirmed              (champ_confirmation doit être identique)
 *   unique:table,column[,exceptId]   (PDO requis via setPdo)
 *
 * Usage :
 *   $v = new Validator($_POST);
 *   $v->rules([
 *      'email'    => 'required|email|max:180|unique:users,email',
 *      'password' => 'required|min:8|confirmed',
 *      'titre'    => 'required|string|min:3|max:200',
 *      'type'     => 'required|in:CDI,CDD,CDIV',
 *   ]);
 *   if ($v->fails()) { $errors = $v->errors(); }
 *   $clean = $v->validated();
 */
class Validator {
    private array $data;
    private array $errors  = [];
    private array $rules   = [];
    private array $labels  = [];
    private ?PDO  $pdo     = null;

    /** Messages par défaut, surchargeables par champ via labels(). */
    private const MESSAGES = [
        'required' => 'Le champ %s est obligatoire.',
        'string'   => 'Le champ %s doit être une chaîne de caractères.',
        'integer'  => 'Le champ %s doit être un entier.',
        'numeric'  => 'Le champ %s doit être numérique.',
        'email'    => 'L\'adresse email %s est invalide.',
        'url'      => 'L\'URL %s est invalide.',
        'min'      => 'Le champ %s doit contenir au moins %s caractères/valeur.',
        'max'      => 'Le champ %s ne peut pas dépasser %s caractères/valeur.',
        'between'  => 'Le champ %s doit être compris entre %s et %s.',
        'in'       => 'Valeur invalide pour %s.',
        'regex'    => 'Le format de %s est invalide.',
        'confirmed'=> 'La confirmation de %s ne correspond pas.',
        'unique'   => 'Cette valeur de %s est déjà utilisée.',
    ];

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function setPdo(PDO $pdo): self {
        $this->pdo = $pdo;
        return $this;
    }

    /** Étiquettes lisibles pour les messages (ex: ['titre' => 'Titre du contrat']). */
    public function labels(array $labels): self {
        $this->labels = $labels;
        return $this;
    }

    public function rules(array $rules): self {
        $this->rules = $rules;
        $this->run();
        return $this;
    }

    public function fails(): bool   { return !empty($this->errors); }
    public function passes(): bool  { return empty($this->errors); }
    public function errors(): array { return $this->errors; }
    public function firstError(string $field): ?string {
        return $this->errors[$field][0] ?? null;
    }

    /** Retourne uniquement les champs déclarés dans les règles, trim sur les strings. */
    public function validated(): array {
        $out = [];
        foreach (array_keys($this->rules) as $field) {
            if (!array_key_exists($field, $this->data)) continue;
            $v = $this->data[$field];
            $out[$field] = is_string($v) ? trim($v) : $v;
        }
        return $out;
    }

    private function run(): void {
        foreach ($this->rules as $field => $ruleStr) {
            $rules = is_array($ruleStr) ? $ruleStr : explode('|', $ruleStr);
            $value = $this->data[$field] ?? null;
            $isPresent = !($value === null || $value === '' || (is_array($value) && count($value) === 0));

            // Si pas required et vide → skip les autres règles (sauf confirmed)
            $hasRequired = in_array('required', $rules, true);
            if (!$hasRequired && !$isPresent) continue;

            foreach ($rules as $r) {
                [$rule, $params] = $this->parseRule($r);
                $ok = $this->applyRule($rule, $value, $params, $field);
                if (!$ok) {
                    $this->addError($field, $rule, $params);
                    break; // une erreur par champ
                }
            }
        }
    }

    private function parseRule(string $r): array {
        if (!str_contains($r, ':')) return [$r, []];
        [$name, $arg] = explode(':', $r, 2);
        return [$name, explode(',', $arg)];
    }

    private function applyRule(string $rule, $value, array $params, string $field): bool {
        switch ($rule) {
            case 'required':
                return !($value === null || $value === '' || (is_array($value) && count($value) === 0));
            case 'string':
                return is_string($value);
            case 'integer':
                return is_int($value) || (is_string($value) && ctype_digit(ltrim($value, '-')));
            case 'numeric':
                return is_numeric($value);
            case 'email':
                return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            case 'url':
                return is_string($value) && filter_var($value, FILTER_VALIDATE_URL) !== false;
            case 'min':
                $n = (int)$params[0];
                return is_numeric($value)
                    ? (float)$value >= $n
                    : mb_strlen((string)$value) >= $n;
            case 'max':
                $n = (int)$params[0];
                return is_numeric($value)
                    ? (float)$value <= $n
                    : mb_strlen((string)$value) <= $n;
            case 'between':
                $lo = (float)$params[0]; $hi = (float)$params[1];
                $v = is_numeric($value) ? (float)$value : mb_strlen((string)$value);
                return $v >= $lo && $v <= $hi;
            case 'in':
                return in_array((string)$value, $params, true);
            case 'regex':
                return is_string($value) && @preg_match($params[0] ?? '', $value) === 1;
            case 'confirmed':
                return ($value === ($this->data[$field . '_confirmation'] ?? null));
            case 'unique':
                if (!$this->pdo) return true; // skip si pas de PDO
                $table  = preg_replace('/[^a-zA-Z0-9_]/', '', $params[0] ?? '');
                $column = preg_replace('/[^a-zA-Z0-9_]/', '', $params[1] ?? $field);
                $exceptId = isset($params[2]) ? (int)$params[2] : 0;
                $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
                $args = [$value];
                if ($exceptId > 0) { $sql .= ' AND id != ?'; $args[] = $exceptId; }
                $st = $this->pdo->prepare($sql);
                $st->execute($args);
                return (int)$st->fetchColumn() === 0;
        }
        return true; // règle inconnue → ignorée
    }

    private function addError(string $field, string $rule, array $params): void {
        $label   = $this->labels[$field] ?? $field;
        $tpl     = self::MESSAGES[$rule] ?? '%s : règle "' . $rule . '" non respectée.';
        $msgArgs = array_merge([$label], $params);
        $message = @vsprintf($tpl, $msgArgs);
        if ($message === false) $message = sprintf('%s : valeur invalide.', $label);
        $this->errors[$field][] = $message;
    }
}
