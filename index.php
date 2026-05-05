<?php
// index.php — single entry point. session_start() en premier, une seule fois.
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=creatorspace;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur DB: " . $e->getMessage());
}

require_once 'Model/Entity.php';

$ctrl   = $_GET['ctrl']   ?? 'auth';
$action = $_GET['action'] ?? 'login';

require_once 'Controller/EntityController.php';
$controller = new EntityController($pdo);

switch ($ctrl) {

    case 'user':
        $allowed = ['index', 'dashboard', 'delete', 'detail', 'profile', 'updateProfile', 'deleteOwn', 'searchUsers', 'publicProfile', 'statistics', 'toggleVerify', 'toggleBan', 'uploadAvatar', 'exportPdf', 'exportStats', 'verifyIdentity'];
        if (!in_array($action, $allowed, true)) {
            header('Location: index.php?ctrl=auth&action=login');
            exit;
        }
        $controller->$action();
        break;

    case 'demande':
        $allowed = ['liste', 'accepter', 'refuser'];
        if (!in_array($action, $allowed, true)) {
            header('Location: index.php?ctrl=demande&action=liste');
            exit;
        }
        $controller->$action();
        break;

    case 'auth':
    default:
        $allowed = ['login', 'register', 'logout', 'error'];
        if (!in_array($action, $allowed, true)) {
            $action = 'login';
        }
        $controller->$action();
        break;
}
