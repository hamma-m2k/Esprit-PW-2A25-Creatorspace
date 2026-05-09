<?php
require_once ROOT . '/Controllers/BaseController.php';
require_once ROOT . '/Controllers/Mailer.php';
require_once ROOT . '/Models/RequestModel.php';
require_once ROOT . '/Models/UserModel.php';
require_once ROOT . '/Models/HistoryModel.php';

class RequestController extends Controller {

    private RequestModel $requestModel;
    private UserModel $userModel;
    private HistoryModel $history;

    public function __construct() {
        $this->requestModel = new RequestModel();
        $this->userModel    = new UserModel();
        $this->history      = new HistoryModel();
    }

    public function index(): void {
        $this->requireAuth();
        $requests = $this->requestModel->getAll();
        $filter   = $_GET['status'] ?? 'all';
        if ($filter !== 'all') {
            $requests = array_filter($requests, fn($r) => $r['status'] === $filter);
        }
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        $this->render('requests/index', compact('requests', 'filter', 'success'));
    }

    public function view(string $id): void {
        $this->requireAuth();
        $request = $this->requestModel->findById((int)$id);
        $this->render('requests/view', compact('request'));
    }

    public function approve(string $id): void {
        $this->requireRole('admin', 'superadmin');
        $this->csrfCheck();
        $request = $this->requestModel->findById((int)$id);

        if ($request && $request['status'] === 'pending') {
            $this->requestModel->updateStatus((int)$id, 'approved', $_SESSION['user_id']);

            // Mot de passe temporaire à transmettre par email
            $tempPassword = bin2hex(random_bytes(6));

            $this->userModel->create([
                'firstname' => $request['firstname'],
                'lastname'  => $request['lastname'],
                'email'     => $request['email'],
                'password'  => $tempPassword,
                'role_id'   => 3,
                'status'    => 'active',
            ]);

            $this->notifyApproval($request, $tempPassword);
            $this->history->log($_SESSION['user_id'], 'REQUEST_APPROVE', "Demande #$id approuvée");
            $_SESSION['success'] = 'Demande approuvée, compte créé et email envoyé.';
        }
        $this->redirect('/requests');
    }

    public function reject(string $id): void {
        $this->requireRole('admin', 'superadmin');
        $this->csrfCheck();
        $this->requestModel->updateStatus((int)$id, 'rejected', $_SESSION['user_id']);
        $this->history->log($_SESSION['user_id'], 'REQUEST_REJECT', "Demande #$id rejetée");
        $_SESSION['success'] = 'Demande rejetée.';
        $this->redirect('/requests');
    }

    /** Best-effort : si SMTP n'est pas configuré, on log et on continue sans planter. */
    private function notifyApproval(array $request, string $tempPassword): void {
        if (!defined('SMTP_HOST') || SMTP_HOST === '' || SMTP_HOST === 'localhost') return;
        try {
            $mailer  = new Mailer();
            $name    = htmlspecialchars($request['firstname']);
            $email   = htmlspecialchars($request['email']);
            $pwd     = htmlspecialchars($tempPassword);
            $loginUrl= BASE_URL . '/';
            $html = <<<HTML
                <h2>Bienvenue sur CreatorSpace, $name !</h2>
                <p>Votre demande d'accès a été <strong>approuvée</strong>.</p>
                <p>Vos identifiants temporaires :</p>
                <ul>
                    <li><strong>Email :</strong> $email</li>
                    <li><strong>Mot de passe :</strong> <code>$pwd</code></li>
                </ul>
                <p>Connectez-vous ici : <a href="$loginUrl">$loginUrl</a></p>
                <p style="color:#888;font-size:12px;">Pensez à changer votre mot de passe à la première connexion.</p>
HTML;
            $mailer->send($request['email'], 'Votre accès CreatorSpace', $html);
        } catch (\Throwable $e) {
            error_log('[Mailer] Notification échouée : ' . $e->getMessage());
        }
    }
}
