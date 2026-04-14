<?php
require_once __DIR__ . '/../model/User.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // SECURITY — only Mohamed Marzougui (admin) passes
    public function isAdmin() {
        if (!isset($_SESSION['role'])       ||
            $_SESSION['role']   !== 'admin' ||
            $_SESSION['nom']    !== 'Mohamed' ||
            $_SESSION['prenom'] !== 'Marzougui') {
            header('Location: index.php?action=login&error=access');
            exit;
        }
    }

    // LOGIN
    public function login() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail     = trim($_POST['mail']     ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($mail === '' || $password === '') {
                $error = "Tous les champs sont obligatoires.";
            } else {
                $user = $this->userModel->findByMail($mail);
                if ($user && $user['password'] === $password) {
                    if ($user['role'] !== 'admin') {
                        $error = "Accès refusé. Seul l'administrateur peut se connecter.";
                    } else {
                        $_SESSION['id']     = $user['id'];
                        $_SESSION['nom']    = $user['nom'];
                        $_SESSION['prenom'] = $user['prenom'];
                        $_SESSION['mail']   = $user['mail'];
                        $_SESSION['role']   = $user['role'];
                        header('Location: index.php?action=dashboard');
                        exit;
                    }
                } else {
                    $error = "Mail ou mot de passe incorrect.";
                }
            }
        }
        include __DIR__ . '/../view/frontoffice/login.php';
    }

    // LOGOUT
    public function logout() {
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }

    // DASHBOARD — admin only
    public function dashboard() {
        $this->isAdmin();
        $totalUsers  = count($this->userModel->getAll());
        $totalAdmins = $this->userModel->countByRole('admin');
        $totalSimple = $this->userModel->countByRole('user');
        include __DIR__ . '/../view/backoffice/dashboard.php';
    }

    // LIST ALL USERS
    public function index() {
        $this->isAdmin();
        $users   = $this->userModel->getAll();
        $success = $_GET['success'] ?? '';
        include __DIR__ . '/../view/backoffice/list_users.php';
    }

    // CREATE USER
    public function create() {
        $this->isAdmin();
        $errors = [];
        $data   = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateForm($_POST);
            if (empty($errors)) {
                $this->userModel->create($_POST);
                header('Location: index.php?action=list&success=ajout');
                exit;
            }
            $data = $_POST;
        }
        include __DIR__ . '/../view/backoffice/add_user.php';
    }

    // EDIT USER
    public function edit() {
        $this->isAdmin();
        $errors = [];
        $id     = $_GET['id'] ?? null;
        $user   = $this->userModel->getById($id);
        if (!$user) {
            header('Location: index.php?action=list');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateForm($_POST);
            if (empty($errors)) {
                $this->userModel->update($id, $_POST);
                header('Location: index.php?action=list&success=modif');
                exit;
            }
            $user = array_merge($user, $_POST);
        }
        include __DIR__ . '/../view/backoffice/edit_user.php';
    }

    // DELETE USER
    public function delete() {
        $this->isAdmin();
        $id = $_GET['id'] ?? null;
        if ($id) $this->userModel->delete($id);
        header('Location: index.php?action=list&success=suppression');
        exit;
    }

    // VALIDATION — PHP only, no HTML5
    private function validateForm($data) {
        $errors   = [];
        $nom      = trim($data['nom']      ?? '');
        $prenom   = trim($data['prenom']   ?? '');
        $mail     = trim($data['mail']     ?? '');
        $password = trim($data['password'] ?? '');

        if ($nom === '')      $errors[] = "Le Nom est obligatoire.";
        if ($prenom === '')   $errors[] = "Le Prénom est obligatoire.";
        if ($mail === '')     $errors[] = "Le Mail est obligatoire.";
        if ($password === '') $errors[] = "Le Mot de passe est obligatoire.";

        if ($nom !== '' && !preg_match('/^[a-zA-ZÀ-ÿ]+$/u', $nom))
            $errors[] = "Le Nom doit contenir uniquement des lettres.";
        if ($prenom !== '' && !preg_match('/^[a-zA-ZÀ-ÿ]+$/u', $prenom))
            $errors[] = "Le Prénom doit contenir uniquement des lettres.";
        if ($mail !== '' && !preg_match('/^[a-zA-Z0-9._%+\-]+@gmail\.com$/', $mail))
            $errors[] = "Le mail doit être au format exemple@gmail.com.";
        if ($password !== '' && !preg_match('/^\d+$/', $password))
            $errors[] = "Le mot de passe doit contenir uniquement des chiffres.";
        if ($password !== '' && strlen($password) < 4)
            $errors[] = "Le mot de passe doit avoir au moins 4 chiffres.";

        return $errors;
    }
}
