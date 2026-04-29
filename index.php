<?php
// index.php — single entry point. session_start() en premier, une seule fois.
session_start();

require_once 'Model/config.php';
require_once 'Model/Entity.php';

$ctrl   = $_GET['ctrl']   ?? 'auth';
$action = $_GET['action'] ?? 'login';

require_once 'Controller/EntityController.php';
$controller = new EntityController($pdo);

switch ($ctrl) {

    case 'user':
        $allowed = ['index', 'dashboard', 'delete', 'detail', 'profile', 'updateProfile', 'deleteOwn', 'searchUsers', 'publicProfile', 'statistics', 'toggleVerify', 'toggleBan', 'uploadAvatar'];
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
