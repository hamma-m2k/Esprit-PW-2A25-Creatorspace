<?php
require_once ROOT . '/Controllers/BaseController.php';
require_once ROOT . '/Models/UserModel.php';
require_once ROOT . '/Models/RequestModel.php';
require_once ROOT . '/Models/HistoryModel.php';

class AuthController extends Controller {

    private UserModel $userModel;
    private RequestModel $requestModel;
    private HistoryModel $history;

    public function __construct() {
        $this->userModel    = new UserModel();
        $this->requestModel = new RequestModel();
        $this->history      = new HistoryModel();
    }

    public function loginPage(): void {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);
        $this->renderAuth('auth/login', compact('error'));
    }

    public function login(): void {
        Csrf::check();
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Veuillez remplir tous les champs.';
            $this->redirect('/');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['login_error'] = 'Adresse email invalide.';
            $this->redirect('/');
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $_SESSION['login_error'] = 'Email ou mot de passe incorrect.';
            $this->redirect('/');
            return;
        }

        if ($user['status'] !== 'active') {
            $_SESSION['login_error'] = 'Votre compte est désactivé.';
            $this->redirect('/');
            return;
        }

        // Anti-fixation : régénère l'ID de session après l'élévation de privilèges
        session_regenerate_id(true);
        Csrf::rotate();

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name']  = $user['firstname'] . ' ' . $user['lastname'];
        $_SESSION['user_role']  = $user['role_name'] ?? 'user';

        $this->history->log($user['id'], 'LOGIN', 'Connexion réussie');
        $this->redirect('/dashboard');
    }

    public function logout(): void {
        if (isset($_SESSION['user_id'])) {
            $this->history->log($_SESSION['user_id'], 'LOGOUT', 'Déconnexion');
        }
        session_destroy();
        $this->redirect('/');
    }

    public function registerPage(): void {
        $error   = $_SESSION['register_error'] ?? null;
        $success = $_SESSION['register_success'] ?? null;
        unset($_SESSION['register_error'], $_SESSION['register_success']);
        $this->renderAuth('auth/register', compact('error', 'success'));
    }

    public function register(): void {
        Csrf::check();
        $firstname    = trim($_POST['firstname'] ?? '');
        $lastname     = trim($_POST['lastname'] ?? '');
        $email        = trim($_POST['email'] ?? '');
        $accountType  = trim($_POST['account_type'] ?? 'standard');
        $message      = trim($_POST['message'] ?? '');

        if (empty($firstname) || empty($lastname) || empty($email)) {
            $_SESSION['register_error'] = 'Veuillez remplir tous les champs obligatoires.';
            $this->redirect('/register');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['register_error'] = 'Adresse email invalide.';
            $this->redirect('/register');
            return;
        }

        if (strlen($firstname) < 2 || strlen($firstname) > 50) {
            $_SESSION['register_error'] = 'Le prénom doit contenir entre 2 et 50 caractères.';
            $this->redirect('/register');
            return;
        }

        $this->requestModel->create([
            'firstname'    => $firstname,
            'lastname'     => $lastname,
            'email'        => $email,
            'account_type' => $accountType,
            'message'      => $message,
        ]);

        $_SESSION['register_success'] = 'Votre demande a été envoyée. Un administrateur l\'examinera bientôt.';
        $this->redirect('/register');
    }
}
