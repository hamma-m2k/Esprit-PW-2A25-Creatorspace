<?php
/**
 * CreatorSpace — ProfileController
 * REFACTOR: Auth check uses SessionManager. Profile lookup uses dynamic user ID.
 * FIX: Hardcoded findById(1) replaced with actual logged-in user ID.
 * FIX: Auth check uses SessionManager::isLoggedIn() — not AuthModel.
 */
class ProfileController
{
    private UserModel $userModel;

    public function __construct()
    {
        // FIX: UserModel requires $pdo — load config first
        require_once __DIR__ . '/../model/config.php';
        require_once __DIR__ . '/../model/UserModel.php';
        $this->userModel = new UserModel($pdo);
    }

    public function show(): void
    {
        // FIX: Auth guard via SessionManager — not AuthModel (Models must not handle HTTP).
        if (!SessionManager::isLoggedIn()) {
            SessionManager::setFlash('error', 'Veuillez vous connecter pour accéder à votre profil.');
            header('Location: index.php?page=home');
            exit;
        }

        $currentUser = SessionManager::getUser();

        // FIX: Dynamic user ID from session — was hardcoded findById(1).
        $profile = $this->userModel->findById($currentUser['id']) ?? $currentUser;

        $this->render('frontoffice/profile', [
            'currentUser' => $currentUser,
            'profile'     => $profile,
            'page'        => 'profile',
        ]);
    }

    private function render(string $view, array $data): void
    {
        extract($data);
        require_once __DIR__ . '/../view/' . $view . '.php';
    }
}
