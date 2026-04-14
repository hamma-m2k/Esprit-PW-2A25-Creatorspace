<?php
// index.php — single entry point. session_start() en premier, une seule fois.
session_start();

$ctrl   = $_GET['ctrl']   ?? 'auth';
$action = $_GET['action'] ?? 'login';

switch ($ctrl) {
    case 'user':
        require_once 'controller/UserController.php';
        break;
    case 'auth':
    default:
        require_once 'controller/AuthController.php';
        break;
}
