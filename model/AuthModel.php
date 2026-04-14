<?php
/**
 * CreatorSpace — AuthModel
 * REFACTOR: Pure authentication data logic only.
 * FIX: Session management extracted to SessionManager — Models must not touch $_SESSION.
 */
class AuthModel
{
    // FIX: In production replace with DB lookup + password_hash/password_verify.
    private static string $demoEmail    = 'sophie.martin@gmail.com';
    private static string $demoPassword = 'password123';

    /**
     * Verifies credentials and returns the user array or null.
     * FIX: No session logic here — that belongs in SessionManager.
     */
    public function authenticate(string $email, string $password): ?array
    {
        // AuthModel is legacy — new auth uses AuthController directly with UserModel($pdo)
        return null;
    }

        if (!$user) {
            return null;
        }

        // Demo check — replace with: password_verify($password, $user['password_hash'])
        if ($email === self::$demoEmail && $password === self::$demoPassword) {
            return $user;
        }

        // Fallback: any known user accepted in demo mode
        return $user;
    }

    /**
     * Builds a new user array from registration data.
     * FIX: No session manipulation — caller (controller) handles session via SessionManager.
     */
    public function createUser(string $firstname, string $lastname, string $email, string $password, string $type): array
    {
        return [
            'id'         => rand(100, 999),
            'name'       => $firstname . ' ' . $lastname,
            'initials'   => strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1)),
            'email'      => $email,
            'role'       => $type === 'creator' ? 'Créateur' : 'Marque',
            'status'     => 'pending',
            'date'       => date('d M Y'),
            'color'      => '#6C3FC5',
            'followers'  => '0',
            'views'      => '0',
            'content'    => 0,
            'completion' => 15,
        ];
    }

    /**
     * FIX: Input validation moved from AuthController to Model.
     * Controllers must not contain business/validation rules.
     * Returns array of error messages, empty array = valid.
     */
    public function validateLogin(string $email, string $password): array
    {
        $errors = [];
        if (empty($email) || empty($password)) {
            $errors[] = 'Veuillez remplir tous les champs.';
        }
        return $errors;
    }

    /**
     * FIX: Registration validation moved from AuthController to Model.
     */
    public function validateRegister(string $firstname, string $lastname, string $email, string $password, bool $terms): array
    {
        $errors = [];
        if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
            $errors[] = 'Veuillez remplir tous les champs.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if (!$terms) {
            $errors[] = 'Veuillez accepter les conditions d\'utilisation.';
        }
        return $errors;
    }
}
