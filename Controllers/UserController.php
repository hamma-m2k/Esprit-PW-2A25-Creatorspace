<?php
require_once ROOT . '/Controllers/BaseController.php';
require_once ROOT . '/Models/UserModel.php';
require_once ROOT . '/Models/RoleModel.php';
require_once ROOT . '/Models/HistoryModel.php';

class UserController extends Controller {

    private UserModel $userModel;
    private RoleModel $roleModel;
    private HistoryModel $history;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->history   = new HistoryModel();
    }

    public function index(): void {
        $this->requireAuth();
        $users = $this->userModel->getAll();
        $this->render('users/index', compact('users'));
    }

    public function create(): void {
        $this->requireRole('admin', 'superadmin');
        $roles  = $this->roleModel->getAll();
        $errors = $_SESSION['form_errors'] ?? [];
        $old    = $_SESSION['form_old'] ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);
        $this->render('users/create', compact('roles', 'errors', 'old'));
    }

    public function store(): void {
        $this->requireRole('admin', 'superadmin');

        $data   = $_POST;
        $errors = $this->validateUser($data);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $data;
            $this->redirect('/users/create');
            return;
        }

        if ($this->userModel->findByEmail($data['email'])) {
            $_SESSION['form_errors'] = ['email' => 'Cet email est déjà utilisé.'];
            $_SESSION['form_old']    = $data;
            $this->redirect('/users/create');
            return;
        }

        $id = $this->userModel->create($data);
        $this->history->log($_SESSION['user_id'], 'USER_CREATE', "Utilisateur #$id créé");
        $_SESSION['success'] = 'Utilisateur créé avec succès.';
        $this->redirect('/users');
    }

    public function edit(string $id): void {
        $this->requireRole('admin', 'superadmin');
        $user   = $this->userModel->findById((int)$id);
        $roles  = $this->roleModel->getAll();
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);
        $this->render('users/edit', compact('user', 'roles', 'errors'));
    }

    public function update(string $id): void {
        $this->requireRole('admin', 'superadmin');
        $data   = $_POST;
        $errors = [];

        if (empty($data['firstname'])) $errors['firstname'] = 'Prénom requis.';
        if (empty($data['lastname']))  $errors['lastname']  = 'Nom requis.';
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide.';
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $this->redirect('/users/edit/' . $id);
            return;
        }

        $this->userModel->update((int)$id, $data);
        $this->history->log($_SESSION['user_id'], 'USER_UPDATE', "Utilisateur #$id modifié");
        $_SESSION['success'] = 'Utilisateur modifié avec succès.';
        $this->redirect('/users');
    }

    public function delete(string $id): void {
        $this->requireRole('admin', 'superadmin');
        $this->userModel->delete((int)$id);
        $this->history->log($_SESSION['user_id'], 'USER_DELETE', "Utilisateur #$id supprimé");
        $_SESSION['success'] = 'Utilisateur supprimé.';
        $this->redirect('/users');
    }

    private function validateUser(array $data): array {
        $errors = [];
        if (empty($data['firstname']) || strlen($data['firstname']) < 2) {
            $errors['firstname'] = 'Prénom invalide (min 2 caractères).';
        }
        if (empty($data['lastname']) || strlen($data['lastname']) < 2) {
            $errors['lastname'] = 'Nom invalide (min 2 caractères).';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Adresse email invalide.';
        }
        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors['password'] = 'Mot de passe trop court (min 8 caractères).';
        }
        return $errors;
    }
}
