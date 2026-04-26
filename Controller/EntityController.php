<?php
/**
 * EntityController — point unique : logique HTTP, validation, orchestration Model / View.
 * Contient également toutes les requêtes (méthodes) de base de données comme demandé.
 */
class EntityController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        require_once __DIR__ . '/../Model/Entity.php';
        $this->pdo = $pdo;
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

    // ==========================================================
    // MÉTHODES DE LOGIQUE DE BASE DE DONNÉES (USER & DEMANDE)
    // ==========================================================

    private function getAllUsers(): array {
        $stmt = $this->pdo->prepare("SELECT id, nom, prenom, mail, role, type_compte, social_media_link, is_accepted FROM `user`");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => User::fromArray($r), $rows);
    }

    private function getUserById(int $id): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM `user` WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? User::fromArray($row) : null;
    }

    private function getUserByMail(string $mail): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM `user` WHERE mail = ? LIMIT 1");
        $stmt->execute([$mail]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? User::fromArray($row) : null;
    }

    private function userMailExiste(string $mail, int $excludeId = 0): bool {
        $stmt = $this->pdo->prepare("SELECT id FROM `user` WHERE mail = ? AND id != ?");
        $stmt->execute([$mail, $excludeId]);
        return $stmt->fetch() !== false;
    }

    private function insertUser(User $user): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `user` (nom, prenom, mail, `password`, role, type_compte, social_media_link, is_accepted)
             VALUES (?, ?, ?, MD5(?), ?, ?, ?, ?)"
        );
        $stmt->execute([
            $user->getNom(),
            $user->getPrenom(),
            $user->getMail(),
            $user->getPassword(),
            $user->getRole(),
            $user->getTypeCompte(),
            $user->getSocialMediaLink(),
            $user->getIsAccepted() ? 1 : 0
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    private function updateUser(User $user): void {
        $stmt = $this->pdo->prepare(
            "UPDATE `user` SET nom=?, prenom=?, mail=?, role=?, type_compte=?, social_media_link=?
             WHERE id=?"
        );
        $stmt->execute([
            $user->getNom(),
            $user->getPrenom(),
            $user->getMail(),
            $user->getRole(),
            $user->getTypeCompte(),
            $user->getSocialMediaLink(),
            $user->getId()
        ]);
    }

    private function updateUserProfile(User $user): void {
        if ($user->getPassword() !== '') {
            $stmt = $this->pdo->prepare(
                "UPDATE `user`
                 SET nom=?, prenom=?, mail=?, `password`=MD5(?), type_compte=?, social_media_link=?
                 WHERE id=?"
            );
            $stmt->execute([
                $user->getNom(),
                $user->getPrenom(),
                $user->getMail(),
                $user->getPassword(),
                $user->getTypeCompte(),
                $user->getSocialMediaLink(),
                $user->getId()
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE `user`
                 SET nom=?, prenom=?, mail=?, type_compte=?, social_media_link=?
                 WHERE id=?"
            );
            $stmt->execute([
                $user->getNom(),
                $user->getPrenom(),
                $user->getMail(),
                $user->getTypeCompte(),
                $user->getSocialMediaLink(),
                $user->getId()
            ]);
        }
    }

    private function deleteUser(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM `user` WHERE id = ?");
        $stmt->execute([$id]);
    }

    private function countAllUsers(): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `user` WHERE is_accepted = 1");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    private function countUsersByRole(string $role): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `user` WHERE `role` = ? AND is_accepted = 1");
        $stmt->execute([$role]);
        return (int)$stmt->fetchColumn();
    }

    private function countUsersByType(string $type): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `user` WHERE `type_compte` = ? AND is_accepted = 1");
        $stmt->execute([$type]);
        return (int)$stmt->fetchColumn();
    }

    private function countNewUsersThisMonth(): int {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM `user`
                 WHERE MONTH(created_at) = MONTH(NOW())
                 AND YEAR(created_at) = YEAR(NOW())"
            );
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    private function getLastFiveUsers(): array {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, prenom, mail, role, type_compte, social_media_link, is_accepted
             FROM `user` ORDER BY id DESC LIMIT 5"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => User::fromArray($r), $rows);
    }

    // Demande DB Logic
    private function getDemandesEnAttente(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM `user` WHERE is_accepted = 0 AND role != 'admin' ORDER BY id ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => User::fromArray($r), $rows);
    }

    private function countDemandesEnAttente(): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `user` WHERE is_accepted = 0 AND role != 'admin'");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    private function accepterDemande(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE `user` SET is_accepted = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    private function refuserDemande(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM `user` WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ==========================================================
    // ── AUTH ──────────────────────────────────────────────────
    // ==========================================================

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

            $user = $this->getUserByMail($mail);
            if (!$user || md5($password) !== $user->getPassword()) {
                $this->redirectError('Email ou mot de passe incorrect.');
            }

            if (!$user->getIsAccepted()) {
                $this->redirectError("Votre compte est en attente d'acceptation par l'administrateur.");
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
            } elseif ($this->userMailExiste($mail, 0)) {
                $errors['mail'] = "Cet email est déjà utilisé ou en attente d'acceptation.";
            }

            if ($password === '') {
                $errors['password'] = "Ce champ est obligatoire.";
            }

            $typesValides = ['user', 'societe', 'createur'];
            if (!in_array($type, $typesValides, true)) {
                $errors['type_compte'] = "Veuillez choisir un type de compte.";
            }

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
                $user = new User();
                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setMail($mail);
                $user->setPassword($password);
                $user->setTypeCompte($type);
                $user->setSocialMediaLink($socialMediaLink);
                $user->setRole('user');
                $user->setIsAccepted(false);
                
                $this->insertUser($user);
                
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

    // ==========================================================
    // ── USER (admin / profil) ─────────────────────────────────
    // ==========================================================

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
        } elseif (empty($errors['mail']) && $this->userMailExiste($mail, $userId)) {
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
        $users              = $this->getAllUsers();
        $total              = count($users);
        $totalPages         = 1;
        $currentPage        = 1;
        $search             = '';
        $roleFilter         = '';
        $statusFilter       = '';
        $page               = 'users';
        $currentUser        = $this->sessionUser();
        $demandesEnAttente  = $this->countDemandesEnAttente();
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
            'total'            => $this->countAllUsers(),
            'admins'           => $this->countUsersByRole('admin'),
            'users'            => $this->countUsersByRole('user'),
            'new_month'        => $this->countNewUsersThisMonth(),
            'societes'         => $this->countUsersByType('societe'),
            'createurs'        => $this->countUsersByType('createur'),
            'normaux'          => $this->countUsersByType('user'),
            'demandes_attente' => $this->countDemandesEnAttente(),
        ];
        $lastUsers         = $this->getLastFiveUsers();
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
        $this->deleteUser($id);
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
            } elseif ($this->userMailExiste($mail, 0)) {
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
                $user = new User();
                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setMail($mail);
                $user->setPassword($password);
                $user->setRole($role);
                $user->setTypeCompte($type);
                $user->setSocialMediaLink($socialMediaLink);
                $user->setIsAccepted(true);

                $this->insertUser($user);
                header('Location: index.php?ctrl=user&action=index&success=creation');
                exit;
            }
            $old = $_POST;
        }

        $page              = 'users';
        $currentUser       = $this->sessionUser();
        $demandesEnAttente = $this->countDemandesEnAttente();
        $this->render('backoffice/form_add', compact(
            'errors', 'old', 'page', 'currentUser', 'demandesEnAttente'
        ));
    }

    public function edit(): void
    {
        $this->checkAdmin();
        $id      = (int)($_GET['id'] ?? 0);
        $userObj = $this->getUserById($id);
        if (!$userObj) {
            $this->redirectError("Utilisateur introuvable.");
        }

        $errors = [];
        $item   = clone $userObj;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom             = trim($_POST['nom']              ?? '');
            $prenom          = trim($_POST['prenom']           ?? '');
            $mail            = trim($_POST['mail']             ?? '');
            $type            = trim($_POST['type_compte']      ?? '');
            $role            = trim($_POST['role']             ?? 'user');
            $socialMediaLink = trim($_POST['social_media_link'] ?? '');

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
            } elseif ($this->userMailExiste($mail, $id)) {
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

            $item->setNom($nom);
            $item->setPrenom($prenom);
            $item->setMail($mail);
            $item->setRole($role);
            $item->setTypeCompte($type);
            $item->setSocialMediaLink($socialMediaLink);

            if (empty($errors)) {
                $this->updateUser($item);
                header('Location: index.php?ctrl=user&action=index&success=modification');
                exit;
            }
        }

        $page              = 'users';
        $currentUser       = $this->sessionUser();
        $demandesEnAttente = $this->countDemandesEnAttente();
        $this->render('backoffice/form_edit', compact(
            'item', 'errors', 'page', 'currentUser', 'demandesEnAttente'
        ));
    }

    public function detail(): void
    {
        $this->checkAdmin();
        $id      = (int)($_GET['id'] ?? 0);
        $userObj = $this->getUserById($id);
        if (!$userObj) {
            $this->redirectError("Utilisateur introuvable.");
        }
        $item              = clone $userObj;
        $page              = 'users';
        $currentUser       = $this->sessionUser();
        $demandesEnAttente = $this->countDemandesEnAttente();
        $this->render('backoffice/detail', compact('item', 'page', 'currentUser', 'demandesEnAttente'));
    }

    public function profile(): void
    {
        $this->checkLogged();
        $errors  = [];
        $userObj = $this->getUserById((int)$_SESSION['user_id']);
        if (!$userObj) {
            header('Location: index.php?ctrl=auth&action=login');
            exit;
        }
        $item              = clone $userObj;
        $profile           = clone $userObj;
        $page              = 'profile';
        $currentUser       = $this->sessionUser();
        $demandesEnAttente = $this->countDemandesEnAttente();
        $successProfile    = $_SESSION['success'] ?? '';
        unset($_SESSION['success']);
        $this->render('backoffice/profile', compact('item', 'profile', 'errors', 'page', 'currentUser', 'demandesEnAttente', 'successProfile'));
    }

    public function updateProfile(): void
    {
        $this->checkLogged();
        $userId = (int)$_SESSION['user_id'];
        $errors = $this->validerProfil($_POST, $userId);
        $userObj = $this->getUserById($userId);
        
        if (empty($errors) && $userObj) {
            $userObj->setNom(trim($_POST['nom'] ?? ''));
            $userObj->setPrenom(trim($_POST['prenom'] ?? ''));
            $userObj->setMail(trim($_POST['mail'] ?? ''));
            $userObj->setTypeCompte(trim($_POST['type_compte'] ?? 'user'));
            $userObj->setSocialMediaLink(trim($_POST['social_media_link'] ?? ''));
            
            if (!empty($_POST['password'])) {
                $userObj->setPassword(trim($_POST['password']));
            } else {
                $userObj->setPassword('');
            }
            
            $this->updateUserProfile($userObj);

            $_SESSION['nom']     = trim($_POST['nom']);
            $_SESSION['mail']    = trim($_POST['mail']);
            $_SESSION['success'] = "Profil mis à jour avec succès.";
            header('Location: index.php?ctrl=user&action=profile');
            exit;
        }

        if (isset($userObj)) {
            $item = clone $userObj;
            $item->setNom(trim($_POST['nom'] ?? ''));
            $item->setPrenom(trim($_POST['prenom'] ?? ''));
            $item->setMail(trim($_POST['mail'] ?? ''));
            $item->setTypeCompte(trim($_POST['type_compte'] ?? 'user'));
            $item->setSocialMediaLink(trim($_POST['social_media_link'] ?? ''));
        } else {
            $item = new User();
        }
        $profile           = clone $item;
        $page              = 'profile';
        $currentUser       = $this->sessionUser();
        $demandesEnAttente = $this->countDemandesEnAttente();
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
        $this->deleteUser((int)$_SESSION['user_id']);
        session_unset();
        session_destroy();
        header('Location: index.php?ctrl=auth&action=login');
        exit;
    }

    // ==========================================================
    // ── DEMANDES ──────────────────────────────────────────────
    // ==========================================================

    public function liste(): void
    {
        $this->checkAdmin();
        $demandes          = $this->getDemandesEnAttente();
        $totalEnAttente    = $this->countDemandesEnAttente();
        $demandesEnAttente = $totalEnAttente;
        $currentUser       = $this->sessionUser();
        $page              = 'demandes';
        $successDemande    = $_SESSION['success_demande'] ?? '';
        unset($_SESSION['success_demande']);
        $this->render('backoffice/demandes', compact(
            'demandes', 'totalEnAttente', 'demandesEnAttente', 'currentUser', 'page', 'successDemande'
        ));
    }



    public function accepter(): void
    {
        $this->checkAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->accepterDemande($id);
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
            $this->refuserDemande($id);
            $_SESSION['success_demande'] = "Demande refusée.";
        }
        header('Location: index.php?ctrl=demande&action=liste');
        exit;
    }

    // ==========================================================
    // ── VIEW ──────────────────────────────────────────────────
    // ==========================================================

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
