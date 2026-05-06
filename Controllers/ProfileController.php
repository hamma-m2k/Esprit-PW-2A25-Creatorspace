<?php
require_once ROOT . '/Controllers/BaseController.php';
require_once ROOT . '/Models/UserModel.php';
require_once ROOT . '/Models/HistoryModel.php';

class ProfileController extends Controller {

    private UserModel $userModel;
    private HistoryModel $history;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->history   = new HistoryModel();
    }

    public function index(): void {
        $this->requireAuth();
        $users = $this->userModel->getAll();
        $this->render('profiles/index', compact('users'));
    }

    public function view(string $id): void {
        $this->requireAuth();
        $user = $this->userModel->findById((int)$id);
        $logs = $this->history->getByUser((int)$id);
        $this->render('profiles/view', compact('user', 'logs'));
    }

    public function update(string $id): void {
        $this->requireAuth();
        if ((int)$id !== (int)$_SESSION['user_id']) {
            $this->requireRole('admin', 'superadmin');
        }

        $data   = $_POST;
        $errors = [];
        if (empty($data['firstname'])) $errors[] = 'Prénom requis.';
        if (empty($data['lastname']))  $errors[] = 'Nom requis.';

        if (!empty($errors)) {
            $_SESSION['profile_errors'] = $errors;
            $this->redirect('/profiles/view/' . $id);
            return;
        }

        $this->userModel->update((int)$id, array_merge(
            $this->userModel->findById((int)$id),
            $data
        ));

        $this->history->log($_SESSION['user_id'], 'PROFILE_UPDATE', "Profil #$id mis à jour");
        $_SESSION['success'] = 'Profil mis à jour.';
        $this->redirect('/profiles/view/' . $id);
    }
}
