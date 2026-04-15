<?php
// controller/AuthController.php — no HTML, no SQL
// session_start() géré dans index.php

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/UserModel.php';

$model  = new UserModel($pdo);
$action = $_GET['action'] ?? 'login';

switch ($action) {

    // ── LOGIN ─────────────────────────────────────────────────
    case 'login':
        // Affiche message succès après inscription
        $success = '';
        if (!empty($_SESSION['success_register'])) {
            $success = $_SESSION['success_register'];
            unset($_SESSION['success_register']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail     = trim($_POST['mail']     ?? '');
            $password = trim($_POST['password'] ?? '');
            $user     = $model->getByMail($mail);

            // Comparaison exacte — role === 'admin' (minuscules)
            if ($user && md5($password) === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nom']     = $user['nom'];
                $_SESSION['role']    = $user['role'];
                $_SESSION['mail']    = $user['mail'];

                if ($user['role'] === 'admin') {
                    header('Location: index.php?ctrl=user&action=dashboard');
                } else {
                    header('Location: index.php?ctrl=user&action=profile');
                }
                exit;
            }

            $error = "Email ou mot de passe incorrect.";
            require_once __DIR__ . '/../view/auth/login.php';

        } else {
            $error = '';
            require_once __DIR__ . '/../view/auth/login.php';
        }
        break;

    // ── REGISTER ──────────────────────────────────────────────
    case 'register':
        // Redirige si déjà connecté
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?ctrl=user&action=profile');
            exit;
        }

        $errors = [];
        $old    = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom      = trim($_POST['nom']      ?? '');
            $prenom   = trim($_POST['prenom']   ?? '');
            $mail     = trim($_POST['mail']     ?? '');
            $password = trim($_POST['password'] ?? '');

            // Validation PHP PURE — SANS HTML5
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
            elseif ($model->mailExiste($mail, 0))
                $errors['mail'] = "Cet email est déjà utilisé.";

            if ($password === '')
                $errors['password'] = "Ce champ est obligatoire.";

            if (empty($errors)) {
                $model->insert([
                    'nom'      => $nom,
                    'prenom'   => $prenom,
                    'mail'     => $mail,
                    'password' => $password,
                    'role'     => 'user',   // rôle forcé
                ]);
                $_SESSION['success_register'] = "Compte créé avec succès ! Connectez-vous.";
                header('Location: index.php?ctrl=auth&action=login');
                exit;
            }

            $old = $_POST;
        }

        require_once __DIR__ . '/../view/auth/register.php';
        break;

    // ── LOGOUT ────────────────────────────────────────────────
    case 'logout':
        session_unset();
        session_destroy();
        header('Location: index.php?ctrl=auth&action=login');
        exit;

    default:
        $error = '';
        require_once __DIR__ . '/../view/auth/login.php';
        break;
}
