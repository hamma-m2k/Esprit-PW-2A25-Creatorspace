<?php
require_once ROOT . '/Controllers/BaseController.php';
require_once ROOT . '/Models/RoleModel.php';
require_once ROOT . '/Models/HistoryModel.php';

class ConfigController extends Controller {

    private RoleModel $roleModel;
    private HistoryModel $history;

    public function __construct() {
        $this->roleModel = new RoleModel();
        $this->history   = new HistoryModel();
    }

    public function roles(): void {
        $this->requireRole('admin', 'superadmin');
        $roles       = $this->roleModel->getAll();
        $permissions = $this->roleModel->getAllPermissions();
        $success     = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        $this->render('config/roles', compact('roles', 'permissions', 'success'));
    }

    public function createRole(): void {
        $this->requireRole('admin', 'superadmin');
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (strlen($name) < 2) {
            $_SESSION['success'] = 'Nom de rôle invalide.';
            $this->redirect('/config/roles');
            return;
        }

        $id = $this->roleModel->create($name, $description);
        if (!empty($_POST['permissions'])) {
            $this->roleModel->setPermissions((int)$id, $_POST['permissions']);
        }

        $this->history->log($_SESSION['user_id'], 'ROLE_CREATE', "Rôle '$name' créé");
        $_SESSION['success'] = "Rôle '$name' créé avec succès.";
        $this->redirect('/config/roles');
    }

    public function deleteRole(string $id): void {
        $this->requireRole('admin', 'superadmin');
        $this->roleModel->delete((int)$id);
        $this->history->log($_SESSION['user_id'], 'ROLE_DELETE', "Rôle #$id supprimé");
        $_SESSION['success'] = 'Rôle supprimé.';
        $this->redirect('/config/roles');
    }

    public function settings(): void {
        $this->requireRole('admin', 'superadmin');
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        $this->render('config/settings', compact('success'));
    }

    public function updateSettings(): void {
        $this->requireRole('admin', 'superadmin');
        // Settings saved to a JSON file for simplicity
        $settings = [
            'site_name'        => trim($_POST['site_name'] ?? APP_NAME),
            'allow_register'   => isset($_POST['allow_register']) ? 1 : 0,
            'items_per_page'   => max(5, (int)($_POST['items_per_page'] ?? 20)),
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0,
        ];
        file_put_contents(ROOT . '/config/settings.json', json_encode($settings, JSON_PRETTY_PRINT));
        $this->history->log($_SESSION['user_id'], 'SETTINGS_UPDATE', 'Paramètres mis à jour');
        $_SESSION['success'] = 'Paramètres enregistrés.';
        $this->redirect('/config/settings');
    }

    public function history(): void {
        $this->requireRole('admin', 'superadmin');
        $logs = $this->history->getAll(200);
        $this->render('config/history', compact('logs'));
    }
}
