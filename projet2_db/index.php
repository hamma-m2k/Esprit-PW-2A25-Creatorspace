<?php
// /index.php — single entry point. No HTML. No SQL. No business logic.

if (session_status() === PHP_SESSION_NONE) session_start();

$ctrl = $_GET['ctrl'] ?? 'auth';

switch ($ctrl) {

    case 'auth':
        require_once 'controller/AuthController.php';
        break;

    case 'utilisateur':
        require_once 'controller/UtilisateurController.php';
        break;

    case 'produit':
        require_once 'controller/ProduitController.php';
        $c = new ProduitController();
        $action = $_GET['action'] ?? 'front';
        if ($action === 'front') {
            $c->front();
        } else {
            $c->handle();
        }
        break;

    default:
        require_once 'controller/AuthController.php';
        break;
}
