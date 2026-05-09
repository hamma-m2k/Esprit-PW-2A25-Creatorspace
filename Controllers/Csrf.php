<?php
/**
 * Protection CSRF par token de session.
 *
 * Usage côté vue :
 *   <?= Csrf::field() ?>
 *
 * Usage côté controller (POST) :
 *   Csrf::check();   // arrête la requête en 419 si invalide
 */
class Csrf {
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    public static function field(): string {
        $t = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="_csrf" value="' . $t . '">';
    }

    public static function verify(?string $token): bool {
        $expected = $_SESSION[self::SESSION_KEY] ?? '';
        return is_string($token) && $expected !== '' && hash_equals($expected, $token);
    }

    public static function check(): void {
        if (!self::verify($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('<h1>419 — Token CSRF invalide ou expiré</h1><p>Merci de recharger la page.</p>');
        }
    }

    public static function rotate(): void {
        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
    }
}
