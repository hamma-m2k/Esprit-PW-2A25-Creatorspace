<?php
// controller/UserController.php — no HTML, no SQL
// session_start() géré dans index.php

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/UserModel.php';

$model  = new UserModel($pdo);
$action = $_GET['action'] ?? 'index';

// ── GUARDS ────────────────────────────────────────────────────

function checkAdmin(): void {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: index.php?ctrl=auth&action=login');
        exit;
    }
}

function checkLogged(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?ctrl=auth&action=login');
        exit;
    }
}

// ── VALIDATION — PHP pure, zéro HTML5 ────────────────────────

function valider(array $data, UserModel $model, int $excludeId = 0): array {
    $errors   = [];
    $nom      = trim($data['nom']      ?? '');
    $prenom   = trim($data['prenom']   ?? '');
    $mail     = trim($data['mail']     ?? '');
    $password = trim($data['password'] ?? '');

    if ($nom === '')      $errors['nom']      = "Ce champ est obligatoire.";
    if ($prenom === '')   $errors['prenom']   = "Ce champ est obligatoire.";
    if ($mail === '')     $errors['mail']     = "Ce champ est obligatoire.";
    if ($password === '') $errors['password'] = "Ce champ est obligatoire.";

    if (empty($errors['nom']) && !preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $nom))
        $errors['nom'] = "Le nom ne doit contenir que des lettres.";

    if (empty($errors['prenom']) && !preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $prenom))
        $errors['prenom'] = "Le prénom ne doit contenir que des lettres.";

    if (empty($errors['mail']) && !str_ends_with($mail, '@gmail.com'))
        $errors['mail'] = "L'email doit se terminer par @gmail.com.";

    if (empty($errors['mail']) && $model->mailExiste($mail, $excludeId))
        $errors['mail'] = "Cet email est déjà utilisé.";

    return $errors;
}

// Validation profil — password optionnel
function validerProfil(array $data, UserModel $model, int $userId): array {
    $errors = [];
    $nom    = trim($data['nom']    ?? '');
    $prenom = trim($data['prenom'] ?? '');
    $mail   = trim($data['mail']   ?? '');

    if ($nom === '')
        $errors['nom'] = "Ce champ est obligatoire.";
    elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $nom))
        $errors['nom'] = "Le nom ne doit contenir que des lettres.";

    if ($prenom === '')
        $errors['prenom'] = "Ce champ est obligatoire.";
    elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $prenom))
        $errors['prenom'] = "Le prénom ne doit contenir que des lettres.";

    if ($mail === '')
        $errors['mail'] = "Ce champ est obligatoire.";
    elseif (!str_ends_with($mail, '@gmail.com'))
        $errors['mail'] = "L'email doit se terminer par @gmail.com.";
    elseif ($model->mailExiste($mail, $userId))
        $errors['mail'] = "Cet email est déjà utilisé.";

    return $errors;
}

function sessionUser(): array {
    return [
        'initials' => strtoupper(substr($_SESSION['nom'] ?? 'U', 0, 2)),
        'name'     => $_SESSION['nom']  ?? '',
        'role'     => $_SESSION['role'] ?? '',
        'color'    => '#6C3FC5',
    ];
}

// ── ROUTER ────────────────────────────────────────────────────

switch ($action) {

    // LIST — admin only
    case 'index':
        checkAdmin();
        $users        = $model->getAll();
        $total        = count($users);
        $totalPages   = 1;
        $currentPage  = 1;
        $search       = '';
        $roleFilter   = '';
        $statusFilter = '';
        $page         = 'users';
        $currentUser  = sessionUser();
        require_once __DIR__ . '/../view/backoffice/list.php';
        break;

    // DASHBOARD — admin only
    case 'dashboard':
        checkAdmin();
        $stats = [
            'total'     => $model->countAll(),
            'admins'    => $model->countByRole('admin'),
            'users'     => $model->countByRole('user'),
            'new_month' => $model->countNewThisMonth(),
        ];
        $lastUsers   = $model->getLastFive();
        $page        = 'dashboard';
        $currentUser = sessionUser();
        require_once __DIR__ . '/../view/backoffice/dashboard.php';
        break;

    // BLOCKED — admin cannot add or edit other users
    case 'create':
    case 'store':
    case 'edit':
    case 'update':
        checkAdmin();
        header('Location: index.php?ctrl=user&action=index');
        exit;

    // DELETE — admin only, cannot delete self
    case 'delete':
        checkAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id === (int)$_SESSION['user_id']) {
            header('Location: index.php?ctrl=user&action=index');
            exit;
        }
        $model->delete($id);
        header('Location: index.php?ctrl=user&action=index&success=suppression');
        exit;

    // REGISTER — public
    case 'register':
        $errors = [];
        $old    = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = valider($_POST, $model);
            if (empty($errors)) {
                $model->insert($_POST);
                $_SESSION['success'] = "Compte créé avec succès ! Connectez-vous.";
                header('Location: index.php?ctrl=auth&action=login');
                exit;
            }
            $old = $_POST;
        }
        require_once __DIR__ . '/../view/frontoffice/register.php';
        break;

    // PROFILE — any logged-in user
    case 'profile':
        checkLogged();
        $errors  = [];
        $profile = $model->getById((int)$_SESSION['user_id']);
        if (!$profile) {
            header('Location: index.php?ctrl=auth&action=login');
            exit;
        }
        $item        = $profile;
        $page        = 'profile';
        $currentUser = sessionUser();
        require_once __DIR__ . '/../view/backoffice/profile.php';
        break;

    // UPDATE PROFILE — any logged-in user
    case 'updateProfile':
        checkLogged();
        $userId = (int)$_SESSION['user_id'];
        $errors = validerProfil($_POST, $model, $userId);
        if (empty($errors)) {
            $model->updateProfile($userId, $_POST);
            $_SESSION['nom']     = trim($_POST['nom']);
            $_SESSION['mail']    = trim($_POST['mail']);
            $_SESSION['success'] = "Profil mis à jour avec succès.";
            header('Location: index.php?ctrl=user&action=profile');
            exit;
        }
        $item        = array_merge($_POST, ['id' => $userId]);
        $profile     = $item;
        $page        = 'profile';
        $currentUser = sessionUser();
        require_once __DIR__ . '/../view/backoffice/profile.php';
        break;

    // DELETE OWN ACCOUNT — non-admin only
    case 'deleteOwn':
        checkLogged();
        if ($_SESSION['role'] === 'admin') {
            header('Location: index.php?ctrl=user&action=profile');
            exit;
        }
        $model->delete((int)$_SESSION['user_id']);
        session_unset();
        session_destroy();
        header('Location: index.php?ctrl=auth&action=login');
        exit;

    default:
        header('Location: index.php?ctrl=auth&action=login');
        exit;
}
