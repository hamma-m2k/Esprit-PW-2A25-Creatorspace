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

    private function mapRowToUser(array $row): User
    {
        return new User(
            (int)($row['id']          ?? 0),
            $row['nom']               ?? '',
            $row['prenom']            ?? '',
            $row['mail']              ?? '',
            $row['password']          ?? '',
            $row['role']              ?? 'user',
            $row['type_compte']       ?? 'user',
            $row['social_media_link'] ?? '',
            $row['created_at']        ?? '',
            (int)($row['followers']   ?? 0),
            (int)($row['following']   ?? 0),
            (bool)($row['is_accepted'] ?? false),
            (bool)($row['is_verified'] ?? false),
            (bool)($row['is_banned']   ?? false),
            $row['profile_picture']   ?? '',
            (bool)($row['two_factor_enabled'] ?? false),
            $row['two_factor_code']   ?? null
        );
    }

    private function countAllUsersWithFilters(string $search = ''): int {
        $query = "SELECT COUNT(*) FROM `user`";
        $params = [];
        if ($search !== '') {
            $query .= " WHERE (nom LIKE ? OR prenom LIKE ? OR mail LIKE ?)";
            $searchParam = "%$search%";
            $params = [$searchParam, $searchParam, $searchParam];
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    private function getAllUsers(string $search = '', string $sort = 'id', int $page = 1, int $perPage = 5): array {
        $query = "SELECT id, nom, prenom, mail, role, type_compte, social_media_link, is_accepted, is_verified, is_banned, profile_picture, created_at, followers, following FROM `user`";
        $params = [];
        
        if ($search !== '') {
            $query .= " WHERE (nom LIKE ? OR prenom LIKE ? OR mail LIKE ?)";
            $searchParam = "%$search%";
            $params = [$searchParam, $searchParam, $searchParam];
        }

        switch ($sort) {
            case 'alphabet': $query .= " ORDER BY nom ASC, prenom ASC"; break;
            case 'date':     $query .= " ORDER BY created_at DESC"; break;
            default:        $query .= " ORDER BY id ASC"; break;
        }

        $offset = ($page - 1) * $perPage;
        $query .= " LIMIT $perPage OFFSET $offset";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->mapRowToUser($r), $rows);
    }

    private function searchAcceptedUsers(string $term): array {
        $query = "SELECT * FROM `user` WHERE is_accepted = 1 AND role != 'admin' AND (nom LIKE ? OR prenom LIKE ?) ORDER BY nom ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(["%$term%", "%$term%"]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->mapRowToUser($r), $rows);
    }

    private function getUserById(int $id): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM `user` WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToUser($row) : null;
    }

    private function getUserByMail(string $mail): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM `user` WHERE mail = ? LIMIT 1");
        $stmt->execute([$mail]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToUser($row) : null;
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
        return array_map(fn($r) => $this->mapRowToUser($r), $rows);
    }

    // Demande DB Logic
    private function getDemandesEnAttente(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM `user` WHERE is_accepted = 0 AND role != 'admin' ORDER BY id ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->mapRowToUser($r), $rows);
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

    private function getInscriptionsParMois(): array {
        $query = "
            SELECT MONTH(created_at) as mois, COUNT(*) as nb 
            FROM `user` 
            WHERE YEAR(created_at) = YEAR(CURRENT_DATE)
            GROUP BY MONTH(created_at)
            ORDER BY mois ASC
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stats = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $stats[(int)$row['mois']] = (int)$row['nb'];
        }
        return $stats;
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

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail     = trim($_POST['mail']     ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($mail === '' || $password === '') {
                $error = 'Veuillez remplir tous les champs.';
            } else {
                $user = $this->getUserByMail($mail);
                if (!$user || md5($password) !== $user->getPassword()) {
                    $error = 'Email ou mot de passe incorrect.';
                } elseif (!$user->getIsAccepted()) {
                    $error = "Votre compte est en attente d'acceptation par l'administrateur.";
                } elseif ($user->getIsBanned()) {
                    $error = "Ton compte a ete banner merci de votre attente.";
                } else {
                    if ($user->getTwoFactorEnabled()) {
                        // Generate code
                        $code = (string)rand(100000, 999999);
                        $stmt = $this->pdo->prepare("UPDATE `user` SET two_factor_code = ? WHERE id = ?");
                        $stmt->execute([$code, $user->getId()]);
                        
                        // Store temp user in session
                        $_SESSION['temp_user_id'] = $user->getId();
                        
                        // Send Email
                        $this->send2FACode($user->getMail(), $code);
                        
                        header('Location: index.php?ctrl=auth&action=verify2FA');
                        exit;
                    }

                    // Standard login
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['nom']     = $user->getNom();
                    $_SESSION['role']    = $user->getRole();
                    $_SESSION['mail']    = $user->getMail();
                    $_SESSION['profile_picture'] = $user->getProfilePicture();

                    if ($user->getRole() === 'admin') {
                        header('Location: index.php?ctrl=user&action=dashboard');
                    } else {
                        header('Location: index.php?ctrl=user&action=profile');
                    }
                    exit;
                }
            }
        }

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

    private function send2FACode(string $email, string $code): void
    {
        require_once __DIR__ . '/../Model/lib/phpmailer/Exception.php';
        require_once __DIR__ . '/../Model/lib/phpmailer/PHPMailer.php';
        require_once __DIR__ . '/../Model/lib/phpmailer/SMTP.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'marzouguim67@gmail.com';
            $mail->Password   = 'rqriwlvnrzvlcbxz'; // <-- Mot de passe d'application Gmail inséré
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Destinataire et Expéditeur
            $mail->setFrom('marzouguim67@gmail.com', 'CreatorSpace Security');
            $mail->addAddress($email);

            // Contenu de l'email
            $mail->isHTML(false);
            $mail->Subject = "Votre code de verification CreatorSpace";
            $mail->Body    = "Bonjour,\n\nVotre code de double authentification est : " . $code . "\n\nSi vous n'avez pas tente de vous connecter, ignorez cet email.";

            $mail->send();
            
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            // Optionnel : gérer l'erreur de manière silencieuse ou via une autre méthode
            // (le fichier de log a été retiré pour garder le projet propre)
        }
    }

    public function verify2FA(): void
    {
        if (empty($_SESSION['temp_user_id'])) {
            header('Location: index.php?ctrl=auth&action=login');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codeInput = trim($_POST['code'] ?? '');
            $userId = $_SESSION['temp_user_id'];
            $user = $this->getUserById($userId);

            if ($user && $codeInput === $user->getTwoFactorCode()) {
                $stmt = $this->pdo->prepare("UPDATE `user` SET two_factor_code = NULL WHERE id = ?");
                $stmt->execute([$userId]);

                $_SESSION['user_id'] = $user->getId();
                $_SESSION['nom']     = $user->getNom();
                $_SESSION['role']    = $user->getRole();
                $_SESSION['mail']    = $user->getMail();
                $_SESSION['profile_picture'] = $user->getProfilePicture();
                unset($_SESSION['temp_user_id']);

                if ($user->getRole() === 'admin') {
                    header('Location: index.php?ctrl=user&action=dashboard');
                } else {
                    header('Location: index.php?ctrl=user&action=profile');
                }
                exit;
            } else {
                $error = "Code incorrect. Veuillez reessayer.";
            }
        }
        $this->render('auth/verify2fa', compact('error'));
    }

    public function toggle2FA(): void
    {
        $this->checkLogged();
        $userId = (int)$_SESSION['user_id'];
        $user = $this->getUserById($userId);
        
        if ($user) {
            $newVal = $user->getTwoFactorEnabled() ? 0 : 1;
            $stmt = $this->pdo->prepare("UPDATE `user` SET two_factor_enabled = ? WHERE id = ?");
            $stmt->execute([$newVal, $userId]);
            
            $_SESSION['success'] = $newVal ? "Double authentification activee." : "Double authentification desactivee.";
        }
        header('Location: index.php?ctrl=user&action=settings');
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
            'profile_picture' => $_SESSION['profile_picture'] ?? ''
        ];
    }

    public function index(): void
    {
        $this->checkAdmin();
        $search      = trim($_GET['search'] ?? '');
        $sort        = $_GET['sort'] ?? 'id';
        $currentPage = (int)($_GET['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        $perPage     = 5;

        $total       = $this->countAllUsersWithFilters($search);
        $totalPages  = ceil($total / $perPage);
        if ($currentPage > $totalPages && $totalPages > 0) $currentPage = $totalPages;

        $users              = $this->getAllUsers($search, $sort, $currentPage, $perPage);
        $roleFilter         = '';
        $statusFilter       = '';
        $page               = 'users';
        $currentUser        = $this->sessionUser();
        $demandesEnAttente  = $this->countDemandesEnAttente();
        $currentUserId      = (int)($_SESSION['user_id'] ?? 0);

        $this->render('backoffice/users', compact(
            'users', 'total', 'totalPages', 'currentPage',
            'search', 'sort', 'roleFilter', 'statusFilter', 'page', 'currentUser',
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

    private function getRegistrationsPerMonth(): array
    {
        $stats = array_fill(1, 12, 0);
        $stmt = $this->pdo->query("
            SELECT MONTH(created_at) as mois, COUNT(*) as nb 
            FROM `user` 
            WHERE YEAR(created_at) = YEAR(CURRENT_DATE) 
            GROUP BY MONTH(created_at)
        ");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stats[(int)$row['mois']] = (int)$row['nb'];
        }
        return $stats;
    }

    private function getUserDistributionByType(): array
    {
        return [
            'Utilisateurs' => $this->countUsersByType('user'),
            'Sociétés'     => $this->countUsersByType('societe'),
            'Créateurs'    => $this->countUsersByType('createur'),
        ];
    }

    public function statistics(): void
    {
        $this->checkAdmin();
        $inscriptionsStats = $this->getRegistrationsPerMonth();
        $distributionStats = $this->getUserDistributionByType();
        
        $page = 'stats';
        $currentUser = $this->sessionUser();
        $demandesEnAttente = $this->countDemandesEnAttente();
        
        $this->render('backoffice/stats', compact(
            'inscriptionsStats', 'distributionStats', 'page', 'currentUser', 'demandesEnAttente'
        ));
    }

    public function exportStats(): void
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['chartImage'])) {
            $img = $_POST['chartImage'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $tmpFile = __DIR__ . '/../View/backoffice/fpdf/temp_chart.png';
            file_put_contents($tmpFile, $data);
            
            $this->exportPdf($tmpFile);
        } else {
            header('Location: index.php?ctrl=user&action=statistics');
            exit;
        }
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

    public function exportPdf(?string $tmpFile = null): void
    {
        $this->checkAdmin();
        require_once __DIR__ . '/../View/backoffice/fpdf/fpdf.php';
        
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // --- COMMON HEADER ---
        $pdf->SetFont('Helvetica', 'B', 22);
        $pdf->SetTextColor(108, 63, 197);
        $pdf->Cell(0, 15, 'CREATOR SPACE', 0, 1, 'C');
        $pdf->SetFont('Helvetica', 'I', 10);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->Cell(0, 5, utf8_decode('Document généré le ' . date('d/m/Y H:i')), 0, 1, 'C');
        $pdf->Ln(10);

        if ($tmpFile && file_exists($tmpFile)) {
            // --- MODE STATISTIQUES ---
            $pdf->SetFont('Helvetica', 'B', 16);
            $pdf->SetTextColor(50, 50, 50);
            $pdf->Cell(0, 10, utf8_decode('Rapport des Statistiques Annuelles'), 0, 1, 'L');
            $pdf->Ln(5);

            // Insertion du Graphique
            $pdf->Image($tmpFile, 15, 50, 180);
            $pdf->Ln(110);

            // Résumé des chiffres
            $stats = [
                'Total Utilisateurs'    => $this->countAllUsers(),
                'Nouveaux ce mois'      => $this->countNewUsersThisMonth(),
                'Utilisateurs Normaux'  => $this->countUsersByType('user'),
                'Createurs'             => $this->countUsersByType('createur'),
                'Societes'              => $this->countUsersByType('societe'),
            ];

            $pdf->SetFillColor(245, 245, 255);
            $pdf->SetFont('Helvetica', 'B', 12);
            foreach ($stats as $label => $val) {
                $pdf->Cell(90, 10, utf8_decode($label), 1, 0, 'L', true);
                $pdf->Cell(90, 10, $val, 1, 1, 'C');
            }
            
            $filename = 'Statistiques_CreatorSpace.pdf';
            if (file_exists($tmpFile)) unlink($tmpFile);
        } else {
            // --- MODE LISTE UTILISATEURS (TOUS) ---
            $pdf->SetFont('Helvetica', 'B', 16);
            $pdf->SetTextColor(50, 50, 50);
            $pdf->Cell(0, 10, utf8_decode('Liste Complète des Utilisateurs'), 0, 1, 'L');
            $pdf->Ln(5);

            // On récupère TOUS les utilisateurs (9999 pour bypasser le limit de 5)
            $users = $this->getAllUsers('', 'id', 1, 9999);

            $pdf->SetFillColor(108, 63, 197);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->Cell(45, 12, 'Nom', 1, 0, 'C', true);
            $pdf->Cell(45, 12, utf8_decode('Prénom'), 1, 0, 'C', true);
            $pdf->Cell(70, 12, 'Email', 1, 0, 'C', true);
            $pdf->Cell(30, 12, utf8_decode('Rôle'), 1, 1, 'C', true);

            $pdf->SetTextColor(50, 50, 50);
            $pdf->SetFont('Helvetica', '', 10);
            $fill = false;
            $pdf->SetFillColor(245, 245, 255);
            foreach ($users as $u) {
                $pdf->Cell(45, 10, utf8_decode($u->getNom()), 1, 0, 'L', $fill);
                $pdf->Cell(45, 10, utf8_decode($u->getPrenom()), 1, 0, 'L', $fill);
                $pdf->Cell(70, 10, utf8_decode($u->getMail()), 1, 0, 'L', $fill);
                $pdf->Cell(30, 10, utf8_decode($u->getRole()), 1, 1, 'C', $fill);
                $fill = !$fill;
            }
            $filename = 'Utilisateurs_CreatorSpace.pdf';
        }

        $pdf->Output('D', $filename);
        exit;
    }




    public function toggleBan(): void
    {
        $this->checkAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id === (int)($_SESSION['user_id'] ?? 0)) {
            header('Location: index.php?ctrl=user&action=index');
            exit;
        }
        $user = $this->getUserById($id);
        if ($user) {
            $newStatus = $user->getIsBanned() ? 0 : 1;
            $stmt = $this->pdo->prepare("UPDATE `user` SET is_banned = ? WHERE id = ?");
            $stmt->execute([$newStatus, $id]);
        }
        header('Location: index.php?ctrl=user&action=index&success=ban');
        exit;
    }

    public function toggleVerify(): void
    {
        $this->checkAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->getUserById($id);
        if ($user) {
            $newStatus = $user->getIsVerified() ? 0 : 1;
            $stmt = $this->pdo->prepare("UPDATE `user` SET is_verified = ? WHERE id = ?");
            $stmt->execute([$newStatus, $id]);
        }
        header('Location: index.php?ctrl=user&action=index&success=verify');
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

    public function uploadAvatar(): void
    {
        $this->checkLogged();
        $userId = (int)$_SESSION['user_id'];
        $userObj = $this->getUserById($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
            $file = $_FILES['profile_picture'];
            
            if ($file['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = mime_content_type($file['tmp_name']);
                
                if (in_array($fileType, $allowedTypes)) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                    $uploadDir = __DIR__ . '/../View/uploads/avatars/';
                    
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                        // Delete old avatar if exists
                        $oldAvatar = $userObj->getProfilePicture();
                        if ($oldAvatar && file_exists(__DIR__ . '/../' . $oldAvatar)) {
                            unlink(__DIR__ . '/../' . $oldAvatar);
                        }
                        
                        $filepath = 'View/uploads/avatars/' . $filename;
                        
                        $stmt = $this->pdo->prepare("UPDATE `user` SET profile_picture = ? WHERE id = ?");
                        $stmt->execute([$filepath, $userId]);
                        
                        $_SESSION['profile_picture'] = $filepath;
                        $_SESSION['success'] = "Photo de profil mise à jour.";
                    }
                } else {
                    $_SESSION['app_error'] = "Format d'image non supporté.";
                }
            }
        }
        header('Location: index.php?ctrl=user&action=profile');
        exit;
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



    public function searchUsers(): void
    {
        $this->checkLogged();
        $term              = trim($_GET['q'] ?? '');
        $users             = $term !== '' ? $this->searchAcceptedUsers($term) : [];
        $page              = 'search';
        $currentUser       = $this->sessionUser();
        $demandesEnAttente = $this->countDemandesEnAttente();
        
        $this->render('backoffice/search_users', compact('users', 'term', 'page', 'currentUser', 'demandesEnAttente'));
    }

    public function publicProfile(): void
    {
        $this->checkLogged();
        $id      = (int)($_GET['id'] ?? 0);
        $userObj = $this->getUserById($id);
        
        if (!$userObj || !$userObj->getIsAccepted()) {
            $_SESSION['app_error'] = "Utilisateur introuvable ou non accepté.";
            header('Location: index.php?ctrl=user&action=searchUsers');
            exit;
        }

        $item              = clone $userObj;
        $page              = 'search';
        $currentUser       = $this->sessionUser();
        $demandesEnAttente = $this->countDemandesEnAttente();
        
        $this->render('backoffice/public_profile', compact('item', 'page', 'currentUser', 'demandesEnAttente'));
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
    public function settings(): void
    {
        $this->checkLogged();
        $user = $this->getUserById((int)$_SESSION['user_id']);
        $page = 'settings';
        $currentUser = $this->sessionUser();
        $demandesEnAttente = $this->countDemandesEnAttente();
        $successMsg = $_SESSION['success'] ?? '';
        unset($_SESSION['success']);
        
        $this->render('backoffice/settings', compact('user', 'page', 'currentUser', 'demandesEnAttente', 'successMsg'));
    }

    public function chatbot(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $userMessage = trim($input['message'] ?? '');

        if ($userMessage === '') {
            echo json_encode(['error' => 'Message cannot be empty']);
            exit;
        }

        // Gemini API Configuration
        $apiKey = 'AIzaSyButi28WQRajySejX0dzlMHZGD8r_aIZDQ'; // <-- Clé Gemini de l'utilisateur insérée
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey;

        // System prompt context
        $systemPrompt = "Tu es l'assistant IA officiel de CreatorSpace, une plateforme web qui met en relation des créateurs de contenu et des sociétés. 
Tu dois répondre aux questions des utilisateurs de manière polie, concise (maximum 3 phrases) et toujours en français. 
Si la question ne concerne pas CreatorSpace, la création de contenu ou la plateforme, dis poliment que tu ne peux répondre qu'aux questions liées à CreatorSpace.";

        $data = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nQuestion de l'utilisateur : " . $userMessage]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        // Désactiver la vérification SSL en local pour éviter les erreurs WAMP/XAMPP
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            echo json_encode(['reply' => "Désolé, je suis actuellement indisponible. (Assurez-vous que la clé API est configurée dans le code)"]);
            exit;
        }

        $result = json_decode($response, true);
        $botReply = $result['candidates'][0]['content']['parts'][0]['text'] ?? "Je n'ai pas pu générer de réponse.";

        echo json_encode(['reply' => trim($botReply)]);
        exit;
    }

    public function generateAiInsights(): void
    {
        $this->checkAdmin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        // Fetch stats
        $inscriptionsStats = $this->getRegistrationsPerMonth();
        $distributionStats = $this->getUserDistributionByType();

        $statsText = "Inscriptions par mois (Jan-Déc): " . implode(', ', $inscriptionsStats) . "\n";
        $statsText .= "Distribution des utilisateurs: ";
        foreach($distributionStats as $type => $count) {
            $statsText .= "$type: $count, ";
        }

        // Gemini API Configuration
        $apiKey = 'AIzaSyButi28WQRajySejX0dzlMHZGD8r_aIZDQ';
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey;

        $systemPrompt = "Tu es un Data Analyst expert pour CreatorSpace (plateforme de créateurs et sociétés). Voici les dernières statistiques de la plateforme.\n\n" . $statsText . "\n\nAgis comme un conseiller stratégique. Fournis une analyse courte, percutante (2 paragraphes maximum) et donne 2 recommandations concrètes à l'administrateur. Rédige ta réponse en HTML formaté (utilise <b>, <ul>, <li>, <br>) pour que le rendu soit beau dans une page web. Ne mets pas de balises markdown comme ```html.";

        $data = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemPrompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            echo json_encode(['error' => "Erreur de l'API IA (Code $httpCode)"]);
            exit;
        }

        $result = json_decode($response, true);
        $insights = $result['candidates'][0]['content']['parts'][0]['text'] ?? "Impossible de générer l'analyse.";

        // Supprimer les balises markdown eventuelles
        $insights = str_replace('```html', '', $insights);
        $insights = str_replace('```', '', $insights);

        echo json_encode(['insights' => trim($insights)]);
        exit;
    }


    public function healthAi(): void
    {
        $this->checkLogged();
        header('Content-Type: application/json');

        // Capture data from POST (more secure for clinical data)
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_GET; // Fallback to GET for simple testing if needed
        }

        $age = (int)($input['age'] ?? 50);
        $trestbps = (int)($input['trestbps'] ?? 120); // Pression systolique
        $chol = (int)($input['chol'] ?? 200);
        $thalach = (int)($input['thalach'] ?? 150); // Fréquence cardiaque max
        $oldpeak = (float)($input['oldpeak'] ?? 0.0); // Dépression ST
        $ca = (int)($input['ca'] ?? 0); // Nb vaisseaux
        $sex = ($input['sex'] ?? 'male') === 'male' ? 1 : 0;
        $exang = (int)($input['exang'] ?? 0); // Angine induite
        $fbs = (int)($input['fbs'] ?? 0); // Glycémie > 120
        $restecg = (int)($input['restecg'] ?? 0);
        $thal = (int)($input['thal'] ?? 0);
        $smoker = (int)($input['smoker'] ?? 0);

        // Gemini API Configuration
        $apiKey = 'AIzaSyButi28WQRajySejX0dzlMHZGD8r_aIZDQ';
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey;

        $systemPrompt = "Tu es un modèle d'IA spécialisé dans l'analyse de risque cardiaque basé sur le dataset Cleveland Heart Disease.
Analyses les données suivantes d'un patient :
- Âge: $age ans
- Sexe: " . ($sex ? 'Masculin' : 'Féminin') . "
- Pression systolique: $trestbps mmHg
- Cholestérol: $chol mg/dL
- Fréquence cardiaque max: $thalach bpm
- Dépression ST (Oldpeak): $oldpeak mm
- Nombre de vaisseaux colorés (CA): $ca
- Angine induite par l'effort: " . ($exang ? 'Oui' : 'Non') . "
- Glycémie à jeun > 120 mg/dL: " . ($fbs ? 'Oui' : 'Non') . "
- ECG au repos: $restecg
- Thalassemia: $thal
- Fumeur actif: " . ($smoker ? 'Oui' : 'Non') . "

Génère une réponse JSON STRICTE (pas de markdown) avec cette structure :
{
  \"diagnostic\": \"Faible risque | Risque Modéré | Risque Élevé\",
  \"score\": int (0-100),
  \"probabilities\": {
    \"absence\": int (%),
    \"stade1\": int (%),
    \"stade2\": int (%),
    \"stade3\": int (%)
  },
  \"importance\": [
    {\"feature\": \"Thalassémie\", \"value\": int (%)},
    {\"feature\": \"Nb. vaisseaux\", \"value\": int (%)},
    {\"feature\": \"Dépression ST\", \"value\": int (%)},
    {\"feature\": \"Fréq. cardiaque\", \"value\": int (%)},
    {\"feature\": \"Âge\", \"value\": int (%)},
    {\"feature\": \"Cholestérol\", \"value\": int (%)}
  ],
  \"conseils\": [\"Conseil 1\", \"Conseil 2\", \"Conseil 3\"]
}";

        $data = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $systemPrompt]]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            echo json_encode(['error' => "Erreur de connexion à l'IA"]);
            exit;
        }

        $result = json_decode($response, true);
        $rawText = $result['candidates'][0]['content']['parts'][0]['text'] ?? "{}";
        
        // Clean markdown if present
        $rawText = str_replace(['```json', '```'], '', $rawText);
        
        echo trim($rawText);
        exit;
    }
}
