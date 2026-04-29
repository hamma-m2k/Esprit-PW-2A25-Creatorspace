<?php
/**
 * Routeur (Front Controller) - Creator Space
 */

// Charger le contrôleur principal
require_once 'controllers/ContratController.php';

$controller = new ContratController();

// Analyser l'URL
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Gérer l'action à exécuter
switch ($action) {
    case 'list': // Page d'accueil publique (FrontOffice)
        $controller->list();
        break;
        
    case 'admin': // Page de gestion interne (BackOffice)
        $controller->admin();
        break;
        
    case 'detail':
        if ($id) {
            $controller->detail($id);
        } else {
            header('Location: index.php');
        }
        break;
        
    case 'add':
        $controller->add();
        break;
        
    case 'edit':
        if ($id) {
            $controller->edit($id);
        } else {
            header('Location: index.php?action=admin');
        }
        break;
        
    case 'delete':
        if ($id) {
            $controller->delete($id);
        } else {
            header('Location: index.php?action=admin');
        }
        break;
        
    default:
        $controller->list();
        break;
}
?>
