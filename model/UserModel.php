<?php
// model/UserModel.php — pure data layer, no HTML, no echo

class UserModel {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT id, nom, prenom, mail, role FROM user");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByMail(string $mail): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE mail = ? LIMIT 1");
        $stmt->execute([$mail]);
        return $stmt->fetch();
    }

    public function insert(array $data): int|false {
        $stmt = $this->pdo->prepare(
            "INSERT INTO user (nom, prenom, mail, password, role)
             VALUES (?, ?, ?, MD5(?), ?)"
        );
        $stmt->execute([
            trim($data['nom']),
            trim($data['prenom']),
            trim($data['mail']),
            trim($data['password']),
            $data['role'] ?? 'user'
        ]);
        return $this->pdo->lastInsertId();
    }

    // update() — admin edit, ne touche pas au password
    public function update(int $id, array $data): void {
        $stmt = $this->pdo->prepare(
            "UPDATE user SET nom=?, prenom=?, mail=?, role=? WHERE id=?"
        );
        $stmt->execute([
            trim($data['nom']),
            trim($data['prenom']),
            trim($data['mail']),
            $data['role'] ?? 'user',
            $id
        ]);
    }

    // updateProfile() — password optionnel : si vide on ne le change pas
    public function updateProfile(int $id, array $data): bool {
        if (!empty(trim($data['password'] ?? ''))) {
            $stmt = $this->pdo->prepare(
                "UPDATE user SET nom=?, prenom=?, mail=?, password=MD5(?) WHERE id=?"
            );
            $stmt->execute([
                trim($data['nom']),
                trim($data['prenom']),
                trim($data['mail']),
                trim($data['password']),
                $id
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE user SET nom=?, prenom=?, mail=? WHERE id=?"
            );
            $stmt->execute([
                trim($data['nom']),
                trim($data['prenom']),
                trim($data['mail']),
                $id
            ]);
        }
        return true;
    }

    // mailExiste() — vérifie unicité email, exclut l'id courant en édition
    public function mailExiste(string $mail, int $excludeId = 0): bool {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM user WHERE mail = ? AND id != ?"
        );
        $stmt->execute([$mail, $excludeId]);
        return $stmt->fetch() !== false;
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM user WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ── Helpers for BackController (original CreatorSpace) ────

    public function findById(int $id): array|false {
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
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE role != 'admin'");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRoles(): array {
        return [
            ['name'=>'admin','icon'=>'🔐','count'=>'1','color'=>'linear-gradient(135deg,#6C3FC5,#9B5DE5)',
             'badge'=>'Admin','badge_class'=>'badge-pro',
             'perms'=>[['label'=>'Accès complet','enabled'=>true],['label'=>'Gestion utilisateurs','enabled'=>true]]],
            ['name'=>'user', 'icon'=>'👤','count'=>'4','color'=>'linear-gradient(135deg,#00C2CB,#00a8b0)',
             'badge'=>'User', 'badge_class'=>'badge-verified',
             'perms'=>[['label'=>'Accès profil','enabled'=>true],['label'=>'Gestion utilisateurs','enabled'=>false]]],
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
