<?php
require_once ROOT . '/Controllers/Csrf.php';

class Controller {
    protected function render(string $view, array $data = []): void {
        extract($data);
        $viewFile = ROOT . '/Views/backoffice/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            die("Vue introuvable : " . htmlspecialchars($view));
        }
        $headerFile = ROOT . '/Views/backoffice/components/header.php';
        $footerFile = ROOT . '/Views/backoffice/components/footer.php';
        if (!file_exists($headerFile) || !file_exists($footerFile)) {
            $headerFile = ROOT . '/Views/backoffice/partials/header.php';
            $footerFile = ROOT . '/Views/backoffice/partials/footer.php';
        }
        require $headerFile;
        require $viewFile;
        require $footerFile;
    }

    protected function renderAuth(string $view, array $data = []): void {
        extract($data);
        $viewFile = ROOT . '/Views/frontoffice/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            die("Vue introuvable : " . htmlspecialchars($view));
        }
        require $viewFile;
    }

    protected function redirect(string $url): void {
        header('Location: ' . BASE_URL . $url);
        exit;
    }

    protected function requireAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    protected function requireRole(string ...$roles): void {
        $this->requireAuth();
        if (!in_array($_SESSION['user_role'] ?? '', $roles, true)) {
            $_SESSION['error'] = 'Accès refusé : permissions insuffisantes.';
            $this->redirect('/dashboard');
        }
    }

    /** True si l'utilisateur courant a un rôle admin ou superadmin. */
    protected function isAdmin(): bool {
        return in_array($_SESSION['user_role'] ?? '', ['admin', 'superadmin'], true);
    }

    /** ID utilisateur courant (0 si déconnecté). */
    protected function currentUserId(): int {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    /** Refuse l'accès si la ressource n'appartient pas à l'utilisateur (sauf admin). */
    protected function requireOwnership(?array $resource, string $ownerKey = 'created_by'): void {
        if (!$resource || (!$this->isAdmin() && (int)($resource[$ownerKey] ?? 0) !== $this->currentUserId())) {
            $_SESSION['error'] = "Accès refusé : ce contenu ne vous appartient pas.";
            $this->redirect('/contrats');
        }
    }

    protected function csrfCheck(): void {
        Csrf::check();
    }

    protected function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** Échappe un string pour HTML, helper court. */
    protected function e(?string $s): string {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
}
