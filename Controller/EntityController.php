<?php
/**
 * EntityController — point unique : logique HTTP, validation, orchestration Model / View.
 */
class EntityController
{
    private UserModel    $userModel;
    private DemandeModel $demandeModel;

    public function __construct(PDO $pdo)
    {
        require_once __DIR__ . '/../Model/Entity.php';
        require_once __DIR__ . '/../Model/UserModel.php';
        require_once __DIR__ . '/../Model/DemandeModel.php';

        $this->userModel    = new UserModel($pdo);
        $this->demandeModel = new DemandeModel($pdo);
    }

    private function redirectError(string $message): void
    {
        $_SESSION['app_error'] = $message;
        header('Location: index.php?ctrl=auth&action=error');
        exit;
    }

    public function error(): void
    {
        $message = $_SESSION['app_error'] ?? 'Une erreur est survenue.';
        unset($_SESSION['app_error']);
        $this->render('frontoffice/error', compact('message'));
    }

    // ── AUTH ──────────────────────────────────────────────────

    public function login(): void
    {
        $success = '';
        if (!empty($_SESSION['success_register'])) {
            $success = $_SESSION['success_register'];
            unset($_SESSION['success_register']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail     = trim($_POST['mail']     ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($mail === '' || $password === '') {
                $this->redirectError('Veuillez remplir tous les champs.');
            }

            $user = $this->userModel->getByMail($mail);
            if (!$user || md5($password) !== $user->getPassword()) {
                $this->redirectError('Email ou mot de passe incorrect.');
            }

            $_SESSION['user_id'] = $user->getId();
            $_SESSION['nom']     = $user->getNom();
            $_SESSION['role']    = $user->getRole();
            $_SESSION['mail']    = $user->getMail();

            if ($user->getRole() === 'admin') {
                header('Location: index.php?ctrl=user&action=dashboard');
            } else {
                header('Location: index.php?ctrl=user&action=profile');
            }
            exit;
        }

        $error       = '';
        $msgRegister = $_SESSION['msg_register'] ?? '';
        unset($_SESSION['msg_register']);
        $this->render('frontoffice/login', compact('error', 'success', 'msgRegister'));
    }

    public function register(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?ctrl=user&action=profile');
            exit;
        }

        $errors = [];
        $old    = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom      = trim($_POST['nom']         ?? '');
            $prenom   = trim($_POST['prenom']      ?? '');
            $mail     = trim($_POST['mail']        ?? '');
            $password = trim($_POST['password']    ?? '');
            $type     = trim($_POST['type_compte'] ?? '');
            $socialMediaLink = trim($_POST['social_media_link'] ?? '');
            $socialMediaPlatform = trim($_POST['social_media_platform'] ?? '');

            if ($nom === '') {
                $errors['nom'] = "Ce champ est obligatoire.";
            } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $nom)) {
                $errors['nom'] = "Le nom ne doit contenir que des lettres.";
            }

            if ($prenom === '') {
                $errors['prenom'] = "Ce champ est obligatoire.";
            } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $prenom)) {
                $errors['prenom'] = "Le prénom ne doit contenir que des lettres.";
            }

            if ($mail === '') {
                $errors['mail'] = "Ce champ est obligatoire.";
            } elseif (!str_ends_with($mail, '@gmail.com')) {
                $errors['mail'] = "L'email doit se terminer par @gmail.com.";
            } elseif ($this->userModel->mailExiste($mail, 0)) {
                $errors['mail'] = "Cet email est déjà utilisé.";
            } elseif ($this->demandeModel->mailEnAttente($mail)) {
                $errors['mail'] = "Une demande est déjà en attente pour cet email.";
            }

            if ($password === '') {
                $errors['password'] = "Ce champ est obligatoire.";
            }

            $typesValides = ['user', 'societe', 'createur'];
            if (!in_array($type, $typesValides, true)) {
                $errors['type_compte'] = "Veuillez choisir un type de compte.";
            }

            // ── Lien social : obligatoire SEULEMENT pour les créateurs ──
            if ($type === 'createur') {
                if ($socialMediaLink === '') {
                    $errors['social_media_link'] = "Le lien réseau social est obligatoire pour un créateur.";
                } elseif (
                    !str_starts_with($socialMediaLink, 'http://') &&
                    !str_starts_with($socialMediaLink, 'https://')
                ) {
                    $errors['social_media_link'] = "Le lien doit commencer par http:// ou https://.";
                }
            } else {
                $socialMediaLink     = '';
                $socialMediaPlatform = '';
            }

            if (empty($errors)) {
                $this->demandeModel->creerDemande([
                    'nom'               => $nom,
                    'prenom'            => $prenom,
                    'mail'              => $mail,
                    'password'          => $password,
                    'type_compte'       => $type,
                    'social_media_link' => $socialMediaLink,
                ]);
                $_SESSION['msg_register'] = "Votre demande a été envoyée. Elle sera traitée par l'administrateur.";
                header('Location: index.php?ctrl=auth&action=login');
                exit;
            }
            $old = $_POST;
        }

        $this->render('frontoffice/register', compact('errors', 'old'));
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: index.php?ctrl=auth&action=login');
        exit;
    }

    // ── USER (admin / profil) ─────────────────────────────────

    private function checkAdmin(): void
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?ctrl=auth&action=login');
            exit;
        }
    }

    private function checkLogged(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?ctrl=auth&action=login');
            exit;
        }
    }

    private function valider(array $data, int $excludeId = 0): array
    {
        $errors   = [];
        $nom      = trim($data['nom']         ?? '');
        $prenom   = trim($data['prenom']      ?? '');
        $mail     = trim($data['mail']        ?? '');
        $password = trim($data['password']    ?? '');
        $type     = trim($data['type_compte'] ?? '');

        if ($nom === '') {
            $errors['nom'] = "Ce champ est obligatoire.";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $nom)) {
            $errors['nom'] = "Le nom ne doit contenir que des lettres.";
        }

        if ($prenom === '') {
            $errors['prenom'] = "Ce champ est obligatoire.";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $prenom)) {
            $errors['prenom'] = "Le prénom ne doit contenir que des lettres.";
        }

        if ($mail === '') {
            $errors['mail'] = "Ce champ est obligatoire.";
        } elseif (!str_ends_with($mail, '@gmail.com')) {
            $errors['mail'] = "L'email doit se terminer par @gmail.com.";
        } elseif (empty($errors['mail']) && $this->userModel->mailExiste($mail, $excludeId)) {
            $errors['mail'] = "Cet email est déjà utilisé.";
        }

        if ($password === '') {
            $errors['password'] = "Ce champ est obligatoire.";
        }

        $typesValides = ['user', 'societe', 'createur'];
        if (!in_array($type, $typesValides, true)) {
            $errors['type_compte'] = "Veuillez choisir un type de compte valide.";
        }

        return $errors;
    }

    private function validerProfil(array $data, int $userId): array
    {
        $errors = [];
        $nom    = trim($data['nom']         ?? '');
        $prenom = trim($data['prenom']      ?? '');
        $mail   = trim($data['mail']        ?? '');
        $type   = trim($data['type_compte'] ?? '');

        if ($nom === '') {
            $errors['nom'] = "Ce champ est obligatoire.";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $nom)) {
            $errors['nom'] = "Le nom ne doit contenir que des lettres.";
        }

        if ($prenom === '') {
            $errors['prenom'] = "Ce champ est obligatoire.";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $prenom)) {
            $errors['prenom'] = "Le prénom ne doit contenir que des lettres.";
        }

        if ($mail === '') {
            $errors['mail'] = "Ce champ est obligatoire.";
        } elseif (!str_ends_with($mail, '@gmail.com')) {
            $errors['mail'] = "L'email doit se terminer par @gmail.com.";
        } elseif (empty($errors['mail']) && $this->userModel->mailExiste($mail, $userId)) {
            $errors['mail'] = "Cet email est déjà utilisé.";
        }

        $typesValides = ['user', 'societe', 'createur'];
        if (!in_array($type, $typesValides, true)) {
            $errors['type_compte'] = "Veuillez choisir un type de compte valide.";
        }

        return $errors;
    }

    private function sessionUser(): array
    {
        return [
            'initials' => strtoupper(substr($_SESSION['nom'] ?? 'U', 0, 2)),
            'name'     => $_SESSION['nom']  ?? '',
            'role'     => $_SESSION['role'] ?? '',
            'color'    => '#6C3FC5',
        ];
    }

    public function index(): void
    {
        $this->checkAdmin();
        $users              = $this->userModel->getAll();
        $total              = count($users);
        $totalPages         = 1;
        $currentPage        = 1;
        $search             = '';
        $roleFilter         = '';
        $statusFilter       = '';
        $page               = 'users';
        $currentUser        = $this->sessionUser();
        $demandesEnAttente  = $this->demandeModel->countEnAttente();
        $currentUserId      = (int)($_SESSION['user_id'] ?? 0);
        $this->render('backoffice/list', compact(
            'users', 'total', 'totalPages', 'currentPage',
            'search', 'roleFilter', 'statusFilter', 'page', 'currentUser',
            'demandesEnAttente', 'currentUserId'
        ));
    }

    public function dashboard(): void
    {
        $this->checkAdmin();
        $stats = [
            'total'            => $this->userModel->countAll(),
            'admins'           => $this->userModel->countByRole('admin'),
            'users'            => $this->userModel->countByRole('user'),
            'new_month'        => $this->userModel->countNewThisMonth(),
            'societes'         => $this->userModel->countByType('societe'),
            'createurs'        => $this->userModel->countByType('createur'),
            'normaux'          => $this->userModel->countByType('user'),
            'demandes_attente' => $this->demandeModel->countEnAttente(),
        ];
        $lastUsers         = $this->userModel->getLastFive();
        $page              = 'dashboard';
        $currentUser       = $this->sessionUser();
        $nomAdmin          = $_SESSION['nom'] ?? 'Admin';
        $demandesEnAttente = $stats['demandes_attente'];
        $this->render('backoffice/dashboard', compact(
            'stats', 'lastUsers', 'page', 'currentUser', 'nomAdmin', 'demandesEnAttente'
        ));
    }

    public function delete(): void
    {
        $this->checkAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id === (int)($_SESSION['user_id'] ?? 0)) {
            header('Location: index.php?ctrl=user&action=index');
            exit;
        }
        $this->userModel->delete($id);
        header('Location: index.php?ctrl=user&action=index&success=suppression');
        exit;
    }

    public function create(): void
    {
        $this->checkAdmin();
        $errors = [];
        $old    = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom             = trim($_POST['nom']              ?? '');
            $prenom          = trim($_POST['prenom']           ?? '');
            $mail            = trim($_POST['mail']             ?? '');
            $password        = trim($_POST['password']         ?? '');
            $type            = trim($_POST['type_compte']      ?? '');
            $role            = trim($_POST['role']             ?? 'user');
            $socialMediaLink = trim($_POST['social_media_link'] ?? '');

            // — Validation —
            if ($nom === '') {
                $errors['nom'] = "Ce champ est obligatoire.";
            } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $nom)) {
                $errors['nom'] = "Le nom ne doit contenir que des lettres.";
            }

            if ($prenom === '') {
                $errors['prenom'] = "Ce champ est obligatoire.";
            } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $prenom)) {
                $errors['prenom'] = "Le prénom ne doit contenir que des lettres.";
            }

            if ($mail === '') {
                $errors['mail'] = "Ce champ est obligatoire.";
            } elseif (!str_ends_with($mail, '@gmail.com')) {
                $errors['mail'] = "L'email doit se terminer par @gmail.com.";
            } elseif ($this->userModel->mailExiste($mail, 0)) {
                $errors['mail'] = "Cet email est déjà utilisé.";
            }

            if ($password === '') {
                $errors['password'] = "Ce champ est obligatoire.";
            }

            $typesValides = ['user', 'societe', 'createur'];
            if (!in_array($type, $typesValides, true)) {
                $errors['type_compte'] = "Veuillez choisir un type de compte valide.";
            }

            $rolesValides = ['user', 'admin'];
            if (!in_array($role, $rolesValides, true)) {
                $errors['role'] = "Rôle invalide.";
            }

            // ── Lien social : obligatoire SEULEMENT pour les créateurs ──
            if ($type === 'createur') {
                if ($socialMediaLink === '') {
                    $errors['social_media_link'] = "Le lien réseau social est obligatoire pour un créateur.";
                } elseif (
                    !str_starts_with($socialMediaLink, 'http://') &&
                    !str_starts_with($socialMediaLink, 'https://')
                ) {
                    $errors['social_media_link'] = "Le lien doit commencer par http:// ou https://.";
                }
            } else {
                $socialMediaLink = '';
            }

            if (empty($errors)) {
                // Utilise l'entité User via les setters avant d'appeler le model
                $user = new User();
                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setMail($mail);
                $user->setPassword($password);
                $user->setRole($role);
                $user->setTypeCompte($type);
                $user->setSocialMediaLink($socialMediaLink);

                $this->userModel->insert($user->toArray() + ['role' => $role]);
                header('Location: index.php?ctrl=user&action=index&success=creation');
                exit;
            }
            $old = $_POST;
        }

        $page              = 'users';
        $currentUser       = $this->sessionUser();
        $demandesEnAttente = $this->demandeModel->countEnAttente();
        $this->render('backoffice/form_add', compact(
            'errors', 'old', 'page', 'currentUser', 'demandesEnAttente'
        ));
    }

    public function edit(): void
    {
        $this->checkAdmin();
        $id      = (int)($_GET['id'] ?? 0);
        $userObj = $this->userModel->getById($id);
        if (!$userObj) {
            $this->redirectError("Utilisateur introuvable.");
        }

        $errors = [];
        $item   = $userObj->toArray();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom             = trim($_POST['nom']              ?? '');
            $prenom          = trim($_POST['prenom']           ?? '');
            $mail            = trim($_POST['mail']             ?? '');
            $type            = trim($_POST['type_compte']      ?? '');
            $role            = trim($_POST['role']             ?? 'user');
            $socialMediaLink = trim($_POST['social_media_link'] ?? '');

            // — Validation —
            if ($nom === '') {
                $errors['nom'] = "Ce champ est obligatoire.";
            } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $nom)) {
                $errors['nom'] = "Le nom ne doit contenir que des lettres.";
            }

            if ($prenom === '') {
                $errors['prenom'] = "Ce champ est obligatoire.";
            } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/u', $prenom)) {
                $errors['prenom'] = "Le prénom ne doit contenir que des lettres.";
            }

            if ($mail === '') {
                $errors['mail'] = "Ce champ est obligatoire.";
            } elseif (!str_ends_with($mail, '@gmail.com')) {
                $errors['mail'] = "L'email doit se terminer par @gmail.com.";
            } elseif ($this->userModel->mailExiste($mail, $id)) {
                $errors['mail'] = "Cet email est déjà utilisé.";
            }

            $typesValides = ['user', 'societe', 'createur'];
            if (!in_array($type, $typesValides, true)) {
                $errors['type_compte'] = "Veuillez choisir un type de compte valide.";
            }

            $rolesValides = ['user', 'admin'];
            if (!in_array($role, $rolesValides, true)) {
                $errors['role'] = "Rôle invalide.";
            }

            if (empty($errors)) {
                // Utilise les setters de l'entité User pour préparer les données
                $userObj->setNom($nom);
                $userObj->setPrenom($prenom);
                $userObj->setMail($mail);
                $userObj->setRole($role);
                $userObj->setTypeCompte($type);
                $userObj->setSocialMediaLink($socialMediaLink);

                $this->userModel->update($id, $userObj->toArray());
                header('Location: index.php?ctrl=user&action=index&success=modification');
                exit;
            }

            // En cas d'erreur, recharger $item avec les données soumises
            $item = array_merge($item, $_POST, ['id' => $id]);
        }

        $page              = 'users';
        $currentUser       = $this->sessionUser();
        $demandesEnAttente = $this->demandeModel->countEnAttente();
        $this->render('backoffice/form_edit', compact(
            'item', 'errors', 'page', 'currentUser', 'demandesEnAttente'
        ));
    }

    public function detail(): void
    {
        $this->checkAdmin();
        $id      = (int)($_GET['id'] ?? 0);
        $userObj = $this->userModel->getById($id);
        if (!$userObj) {
            $this->redirectError("Utilisateur introuvable.");
        }
        $item              = $userObj->toArray();
        $page              = 'users';
        $currentUser       = $this->sessionUser();
        $demandesEnAttente = $this->demandeModel->countEnAttente();
        $this->render('backoffice/detail', compact('item', 'page', 'currentUser', 'demandesEnAttente'));
    }

    public function profile(): void
    {
        $this->checkLogged();
        $errors  = [];
        $userObj = $this->userModel->getById((int)$_SESSION['user_id']);
        if (!$userObj) {
            header('Location: index.php?ctrl=auth&action=login');
            exit;
        }
        $item              = $userObj->toArray();
        $profile           = $item;
        $page              = 'profile';
        $currentUser       = $this->sessionUser();
        $demandesEnAttente = $this->demandeModel->countEnAttente();
        $successProfile    = $_SESSION['success'] ?? '';
        unset($_SESSION['success']);
        $this->render('backoffice/profile', compact('item', 'profile', 'errors', 'page', 'currentUser', 'demandesEnAttente', 'successProfile'));
    }

    public function updateProfile(): void
    {
        $this->checkLogged();
        $userId = (int)$_SESSION['user_id'];
        $errors = $this->validerProfil($_POST, $userId);
        if (empty($errors)) {
            $this->userModel->updateProfile($userId, $_POST);
            $_SESSION['nom']     = trim($_POST['nom']);
            $_SESSION['mail']    = trim($_POST['mail']);
            $_SESSION['success'] = "Profil mis à jour avec succès.";
            header('Location: index.php?ctrl=user&action=profile');
            exit;
        }
        $item              = array_merge($_POST, ['id' => $userId]);
        $profile           = $item;
        $page              = 'profile';
        $currentUser       = $this->sessionUser();
        $demandesEnAttente = $this->demandeModel->countEnAttente();
        $successProfile    = '';
        $this->render('backoffice/profile', compact('item', 'profile', 'errors', 'page', 'currentUser', 'demandesEnAttente', 'successProfile'));
    }

    public function deleteOwn(): void
    {
        $this->checkLogged();
        if ($_SESSION['role'] === 'admin') {
            header('Location: index.php?ctrl=user&action=profile');
            exit;
        }
        $this->userModel->delete((int)$_SESSION['user_id']);
        session_unset();
        session_destroy();
        header('Location: index.php?ctrl=auth&action=login');
        exit;
    }

    // ── DEMANDES ──────────────────────────────────────────────

    public function liste(): void
    {
        $this->checkAdmin();
        $demandes          = $this->demandeModel->getEnAttente();
        $totalEnAttente    = $this->demandeModel->countEnAttente();
        $demandesEnAttente = $totalEnAttente;
        $currentUser       = $this->sessionUser();
        $page              = 'demandes';
        $successDemande    = $_SESSION['success_demande'] ?? '';
        unset($_SESSION['success_demande']);
        $this->render('backoffice/demandes', compact(
            'demandes', 'totalEnAttente', 'demandesEnAttente', 'currentUser', 'page', 'successDemande'
        ));
    }

    public function historique(): void
    {
        $this->checkAdmin();
        $demandes          = $this->demandeModel->getAll();
        $demandesEnAttente = $this->demandeModel->countEnAttente();
        $currentUser       = $this->sessionUser();
        $page              = 'demandes';
        $this->render('backoffice/demandes_historique', compact(
            'demandes', 'demandesEnAttente', 'currentUser', 'page'
        ));
    }

    public function accepter(): void
    {
        $this->checkAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->demandeModel->accepter($id, $this->userModel);
            $_SESSION['success_demande'] = "Compte créé avec succès.";
        }
        header('Location: index.php?ctrl=demande&action=liste');
        exit;
    }

    public function refuser(): void
    {
        $this->checkAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->demandeModel->refuser($id);
            $_SESSION['success_demande'] = "Demande refusée.";
        }
        header('Location: index.php?ctrl=demande&action=liste');
        exit;
    }

    // ── VIEW ──────────────────────────────────────────────────

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = __DIR__ . '/../View/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo '<h1>404 — Vue introuvable : ' . htmlspecialchars($view) . '</h1>';
            return;
        }
        require_once $viewFile;
    }
}
