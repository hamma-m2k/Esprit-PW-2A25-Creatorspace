<?php
// controller/AuthController.php — no HTML, no SQL
// session_start() géré dans index.php — pas de doublon ici

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/UserModel.php';

$model  = new UserModel($pdo);
$action = $_GET['action'] ?? 'login';

switch ($action) {

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail     = trim($_POST['mail']     ?? '');
            $password = trim($_POST['password'] ?? '');
            $user     = $model->getByMail($mail);

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
