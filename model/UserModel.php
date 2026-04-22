<?php
/**
 * UserModel.php — Couche d'accès aux données pour la table `user`
 * Utilise l'entité User (attributs, constructeur, getters/setters)
 * Aucun HTML, aucun echo, aucune logique métier ici.
 */

require_once __DIR__ . '/Entity.php';

class UserModel {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ── Lecture ───────────────────────────────────────────────

    /** Retourne tous les utilisateurs sous forme de tableaux (pour les vues) */
    public function getAll(): array {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, prenom, mail, role, type_compte, social_media_link FROM `user`"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Retourne un User par son ID */
    public function getById(int $id): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM `user` WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? User::fromArray($row) : null;
    }

    /** Retourne un User par son mail */
    public function getByMail(string $mail): ?User {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM `user` WHERE mail = ? LIMIT 1"
        );
        $stmt->execute([$mail]);
        $row = $stmt->fetch();
        return $row ? User::fromArray($row) : null;
    }

    /** Vérifie si un mail existe déjà (exclut l'id courant en édition) */
    public function mailExiste(string $mail, int $excludeId = 0): bool {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM `user` WHERE mail = ? AND id != ?"
        );
        $stmt->execute([$mail, $excludeId]);
        return $stmt->fetch() !== false;
    }

    // ── Écriture ──────────────────────────────────────────────

    /** Insère un nouvel utilisateur, retourne l'ID créé */
    public function insert(array $data): int {
        $user = new User(
            0,
            trim($data['nom']         ?? ''),
            trim($data['prenom']      ?? ''),
            trim($data['mail']        ?? ''),
            trim($data['password']    ?? ''),
            trim($data['role']        ?? 'user'),
            trim($data['type_compte'] ?? 'user'),
            trim($data['social_media_link'] ?? '')
        );

        $stmt = $this->pdo->prepare(
            "INSERT INTO `user` (nom, prenom, mail, `password`, role, type_compte, social_media_link)
             VALUES (?, ?, ?, MD5(?), ?, ?, ?)"
        );
        $stmt->execute([
            $user->getNom(),
            $user->getPrenom(),
            $user->getMail(),
            $user->getPassword(),
            $user->getRole(),
            $user->getTypeCompte(),
            $user->getSocialMediaLink()
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /** Met à jour un utilisateur (admin — ne touche pas au password) */
    public function update(int $id, array $data): void {
        $user = new User(
            $id,
            trim($data['nom']         ?? ''),
            trim($data['prenom']      ?? ''),
            trim($data['mail']        ?? ''),
            '',
            $data['role']             ?? 'user',
            $data['type_compte']      ?? 'user',
            trim($data['social_media_link'] ?? '')
        );

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

    /** Met à jour le profil personnel (password optionnel) */
    public function updateProfile(int $id, array $data): void {
        $user = new User(
            $id,
            trim($data['nom']         ?? ''),
            trim($data['prenom']      ?? ''),
            trim($data['mail']        ?? ''),
            trim($data['password']    ?? ''),
            'user',
            trim($data['type_compte'] ?? 'user'),
            trim($data['social_media_link'] ?? '')
        );

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

    /** Supprime un utilisateur par son ID */
    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM `user` WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ── Statistiques ──────────────────────────────────────────

    public function countAll(): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `user`");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function countByRole(string $role): int {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM `user` WHERE `role` = ?"
        );
        $stmt->execute([$role]);
        return (int)$stmt->fetchColumn();
    }

    public function countByType(string $type): int {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM `user` WHERE `type_compte` = ?"
        );
        $stmt->execute([$type]);
        return (int)$stmt->fetchColumn();
    }

    public function countNewThisMonth(): int {
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

    public function getLastFive(): array {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, prenom, mail, role, type_compte, social_media_link
             FROM `user` ORDER BY id DESC LIMIT 5"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ── Helpers pour BackController (CreatorSpace original) ───

    public function findById(int $id): ?User {
        return $this->getById($id);
    }

    public function getStats(): array {
        $all = $this->getAll();
        return [
            'total'    => count($all),
            'active'   => count($all),
            'creators' => count(array_filter($all, fn($u) => $u['role'] !== 'admin')),
            'verified' => count(array_filter($all, fn($u) => $u['role'] === 'admin')),
        ];
    }

    public function getActivities(): array {
        return [
            ['color'=>'#38a169', 'text'=>'<strong>Mohamed Marzougui</strong> s\'est connecté', 'time'=>'Il y a 2 min'],
            ['color'=>'#6C3FC5', 'text'=>'Un nouvel utilisateur a rejoint la plateforme',       'time'=>'Il y a 8 min'],
            ['color'=>'#00C2CB', 'text'=>'Mise à jour du système effectuée',                    'time'=>'Il y a 15 min'],
        ];
    }

    public function getCreators(): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM `user` WHERE role != 'admin'"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRoles(): array {
        return [
            ['name'=>'admin','icon'=>'🔐','count'=>'1',
             'color'=>'linear-gradient(135deg,#6C3FC5,#9B5DE5)',
             'badge'=>'Admin','badge_class'=>'badge-pro',
             'perms'=>[['label'=>'Accès complet','enabled'=>true]]],
            ['name'=>'user', 'icon'=>'👤','count'=>'4',
             'color'=>'linear-gradient(135deg,#00C2CB,#00a8b0)',
             'badge'=>'User', 'badge_class'=>'badge-verified',
             'perms'=>[['label'=>'Accès profil','enabled'=>true]]],
        ];
    }

    public function search(string $query = '', string $role = '', string $status = ''): array {
        $all = $this->getAll();
        if ($query !== '') {
            $all = array_filter($all, fn($u) =>
                stripos($u['nom'],  $query) !== false ||
                stripos($u['mail'], $query) !== false ||
                stripos($u['role'], $query) !== false
            );
        }
        if ($role !== '') {
            $all = array_filter($all, fn($u) => $u['role'] === $role);
        }
        return array_values($all);
    }

    public function paginate(array $users, int $page, int $perPage = 8): array {
        $total = count($users);
        return [
            'items'       => array_slice($users, ($page - 1) * $perPage, $perPage),
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public static function statusLabel(string $status): string {
        return match($status) {
            'active'   => '● Actif',
            'inactive' => '● Inactif',
            'pending'  => '● En attente',
            default    => $status,
        };
    }

    public static function statusClass(string $status): string {
        return match($status) {
            'active'   => 'status-active',
            'inactive' => 'status-inactive',
            'pending'  => 'status-pending',
            default    => '',
        };
    }
}
