<?php
// /model/UtilisateurModel.php
// Table : user (creatorspace)
// Pure data layer — no HTML, no echo, no business logic.

class UtilisateurModel {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // SELECT id, nom, prenom, mail, role FROM user
    public function getAll(): array {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, prenom, mail, role FROM user"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // SELECT * FROM user WHERE id = ?
    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM user WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // SELECT * FROM user WHERE mail = ? LIMIT 1
    public function getByMail(string $mail): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM user WHERE mail = ? LIMIT 1"
        );
        $stmt->execute([$mail]);
        return $stmt->fetch();
    }

    // INSERT INTO user (nom, prenom, mail, password, role) — password hashed md5
    public function insert(array $data): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO user (nom, prenom, mail, password, role)
             VALUES (?, ?, ?, md5(?), ?)"
        );
        return $stmt->execute([
            trim($data['nom']),
            trim($data['prenom']),
            trim($data['mail']),
            trim($data['password']),
            $data['role'] ?? 'user'
        ]);
    }

    // UPDATE user SET nom, prenom, mail, password WHERE id = ?
    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE user
             SET nom=?, prenom=?, mail=?, password=md5(?)
             WHERE id=?"
        );
        return $stmt->execute([
            trim($data['nom']),
            trim($data['prenom']),
            trim($data['mail']),
            trim($data['password']),
            $id
        ]);
    }

    // UPDATE user SET nom, prenom, mail, password WHERE id = ? (self-edit)
    public function updateProfile(int $id, array $data): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE user
             SET nom=?, prenom=?, mail=?, password=md5(?)
             WHERE id=?"
        );
        return $stmt->execute([
            trim($data['nom']),
            trim($data['prenom']),
            trim($data['mail']),
            trim($data['password']),
            $id
        ]);
    }

    // DELETE FROM user WHERE id = ?
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare(
            "DELETE FROM user WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }
}
