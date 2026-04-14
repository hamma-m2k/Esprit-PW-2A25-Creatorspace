<?php
// /controller/AuthController.php
// Handles login / logout only. No HTML. No SQL.

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/UtilisateurModel.php';

$model  = new UtilisateurModel($pdo);
$action = $_GET['action'] ?? 'login';

switch ($action) {

    // ── LOGIN ─────────────────────────────────────────────────
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $mail     = trim($_POST['mail']     ?? '');
            $password = trim($_POST['password'] ?? '');
            $user     = $model->getByMail($mail);

            if ($user && md5($password) === $user['password']) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nom']     = $user['nom'];
                $_SESSION['role']    = $user['role'];
                $_SESSION['mail']    = $user['mail'];

                if ($user['role'] === 'admin') {
                    header('Location: index.php?ctrl=utilisateur&action=index');
                } else {
                    header('Location: index.php?ctrl=utilisateur&action=profile');
                }
                exit;

            } else {
                $error = "Email ou mot de passe incorrect.";
                include __DIR__ . '/../view/auth/login.php';
            }

        } else {
            // GET — show login form
            include __DIR__ . '/../view/auth/login.php';
        }
        break;

    // ── LOGOUT ────────────────────────────────────────────────
    case 'logout':
        session_start();
        session_destroy();
        header('Location: index.php?ctrl=auth&action=login');
        exit;
}
