<?php
/**
 * CreatorSpace — BackController
 * REFACTOR: Controller is now a pure intermediary.
 * FIX: All filtering/pagination logic moved to UserModel::search() and UserModel::paginate().
 * FIX: Authentication guard added — all backoffice routes require login.
 * FIX: array_values() call removed — UserModel::getCreators() now returns clean array.
 * FIX: Role data removed — now fetched from UserModel::getRoles().
 */
class BackController
{
    private UserModel $userModel;

    public function __construct()
    {
        // FIX: UserModel requires $pdo — load config first
        require_once __DIR__ . '/../model/config.php';
        require_once __DIR__ . '/../model/UserModel.php';
        $this->userModel = new UserModel($pdo);
        $this->requireAuth();
    }

    /**
     * FIX: Authentication check centralized here — not duplicated per method.
     * Previously backoffice was fully accessible without login.
     */
    private function requireAuth(): void
    {
        if (!SessionManager::isLoggedIn()) {
            SessionManager::setFlash('error', 'Accès réservé. Veuillez vous connecter.');
            header('Location: index.php?page=home');
            exit;
        }
    }

    public function dashboard(): void
    {
        $this->render('backoffice/dashboard', [
            'currentUser' => SessionManager::getUser(),
            'stats'       => $this->userModel->getStats(),
            'activities'  => $this->userModel->getActivities(),
            'page'        => 'dashboard',
        ]);
    }

    public function users(): void
    {
        // FIX: Controller only reads request params and passes to Model.
        // Filtering and pagination logic now live in UserModel.
        $search  = trim($_GET['search'] ?? '');
        $role    = $_GET['role'] ?? '';
        $status  = $_GET['status'] ?? '';
        $pageNum = max(1, (int)($_GET['p'] ?? 1));

        // FIX: Filtering delegated to Model::search().
        $filtered = $this->userModel->search($search, $role, $status);

        // FIX: Pagination delegated to Model::paginate().
        $paged = $this->userModel->paginate($filtered, $pageNum);

        $this->render('backoffice/users', [
            'currentUser'  => SessionManager::getUser(),
            'users'        => $paged['items'],
            'total'        => $paged['total'],
            'currentPage'  => $paged['currentPage'],
            'totalPages'   => $paged['totalPages'],
            'search'       => $search,
            'roleFilter'   => $role,
            'statusFilter' => $status,
            'page'         => 'users',
        ]);
    }

    public function profiles(): void
    {
        // FIX: array_values() removed — UserModel::getCreators() returns clean array.
        $this->render('backoffice/profiles', [
            'currentUser' => SessionManager::getUser(),
            'creators'    => $this->userModel->getCreators(),
            'page'        => 'profiles',
        ]);
    }

    public function roles(): void
    {
        // FIX: Role data fetched from Model — was hardcoded in view/backoffice/roles.php.
        $this->render('backoffice/roles', [
            'currentUser' => SessionManager::getUser(),
            'roles'       => $this->userModel->getRoles(),
            'page'        => 'roles',
        ]);
    }

    public function settings(): void
    {
        $this->render('backoffice/settings', [
            'currentUser' => SessionManager::getUser(),
            'page'        => 'settings',
        ]);
    }

    private function render(string $view, array $data): void
    {
        extract($data);
        require_once __DIR__ . '/../view/' . $view . '.php';
    }
}
