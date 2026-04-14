<?php
// /controller/UtilisateurController.php
// No HTML. No SQL. Only: receive request → call Model → pass data to View.

session_start();

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/UtilisateurModel.php';

$model  = new UtilisateurModel($pdo);
$action = $_GET['action'] ?? 'index';

// ── SECURITY GUARDS ───────────────────────────────────────────

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

// ── VALIDATION — PHP only, no HTML5 ──────────────────────────

function validerUtilisateur(array $data, bool $isEdit = false): array {
    $errors = [];

    $nom      = trim($data['nom']      ?? '');
    $prenom   = trim($data['prenom']   ?? '');
    $mail     = trim($data['mail']     ?? '');
    $password = trim($data['password'] ?? '');

    // (a) Empty checks
    if ($nom === '')      $errors['nom']      = "Ce champ est obligatoire.";
    if ($prenom === '')   $errors['prenom']   = "Ce champ est obligatoire.";
    if ($mail === '')     $errors['mail']     = "Ce champ est obligatoire.";
    if (!$isEdit && $password === '') $errors['password'] = "Ce champ est obligatoire.";

    // (b) Email must end with @gmail.com
    if ($mail !== '' && !str_ends_with($mail, '@gmail.com')) {
        $errors['mail'] = "L'email doit se terminer par @gmail.com.";
    }

    // (c) Nom — letters only
    if ($nom !== '' && !preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $nom)) {
        $errors['nom'] = "Le nom ne doit contenir que des lettres.";
    }

    // (d) Prenom — letters only
    if ($prenom !== '' && !preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $prenom)) {
        $errors['prenom'] = "Le prénom ne doit contenir que des lettres.";
    }

    return $errors;
}

// ── ROUTER ────────────────────────────────────────────────────

switch ($action) {

    // LIST — admin only
    case 'index':
        checkAdmin();
        $data = $model->getAll();
        include __DIR__ . '/../view/backoffice/list.php';
        break;

    // SHOW ADD FORM — admin only
    case 'create':
        checkAdmin();
        $errors = [];
        $old    = [];
        include __DIR__ . '/../view/backoffice/form_add.php';
        break;

    // PROCESS ADD — admin only
    case 'store':
        checkAdmin();
        $errors = validerUtilisateur($_POST);
        if (empty($errors)) {
            $model->insert($_POST);
            header('Location: index.php?ctrl=utilisateur&action=index&success=ajout');
            exit;
        }
        $old = $_POST;
        include __DIR__ . '/../view/backoffice/form_add.php';
        break;

    // SHOW EDIT FORM — admin only
    case 'edit':
        checkAdmin();
        $errors = [];
        $item   = $model->getById((int)($_GET['id'] ?? 0));
        if (!$item) {
            header('Location: index.php?ctrl=utilisateur&action=index');
            exit;
        }
        include __DIR__ . '/../view/backoffice/form_edit.php';
        break;

    // PROCESS EDIT — admin only
    case 'update':
        checkAdmin();
        $errors = validerUtilisateur($_POST, true);
        if (empty($errors)) {
            $model->update((int)($_GET['id'] ?? 0), $_POST);
            header('Location: index.php?ctrl=utilisateur&action=index&success=modif');
            exit;
        }
        $item       = $_POST;
        $item['id'] = $_GET['id'] ?? 0;
        include __DIR__ . '/../view/backoffice/form_edit.php';
        break;

    // DELETE — admin only
    case 'delete':
        checkAdmin();
        $model->delete((int)($_GET['id'] ?? 0));
        header('Location: index.php?ctrl=utilisateur&action=index&success=suppression');
        exit;

    // PROFILE — any logged-in user
    case 'profile':
        checkLogged();
        $errors = [];
        $item   = $model->getById((int)$_SESSION['user_id']);
        include __DIR__ . '/../view/backoffice/profile.php';
        break;

    // UPDATE PROFILE — any logged-in user
    case 'updateProfile':
        checkLogged();
        $errors = validerUtilisateur($_POST, true);
        if (empty($errors)) {
            $model->updateProfile((int)$_SESSION['user_id'], $_POST);
            $_SESSION['nom'] = $_POST['nom'];
            header('Location: index.php?ctrl=utilisateur&action=profile&success=modif');
            exit;
        }
        $item       = $_POST;
        $item['id'] = $_SESSION['user_id'];
        include __DIR__ . '/../view/backoffice/profile.php';
        break;

    // DELETE OWN ACCOUNT — logged-in non-admin only
    case 'deleteOwn':
        checkLogged();
        // Admin cannot delete their own account
        if ($_SESSION['role'] === 'admin') {
            header('Location: index.php?ctrl=utilisateur&action=profile&error=admin_nodelete');
            exit;
        }
        $model->delete((int)$_SESSION['user_id']);
        session_destroy();
        header('Location: index.php?ctrl=auth&action=login');
        exit;

    default:
        header('Location: index.php?ctrl=auth&action=login');
        exit;
}
