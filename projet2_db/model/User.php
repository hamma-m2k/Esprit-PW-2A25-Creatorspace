<?php
require_once __DIR__ . '/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Get all users
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM user");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get user by ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Find by mail — column is "mail" NOT "email"
    public function findByMail($mail) {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE mail = ?");
        $stmt->execute([$mail]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create new user
    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO user (nom, prenom, mail, password, role)
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            trim($data['nom']),
            trim($data['prenom']),
            trim($data['mail']),
            trim($data['password']),
            $data['role'] ?? 'user'
        ]);
    }

    // Update user
    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE user
             SET nom=?, prenom=?, mail=?, password=?, role=?
             WHERE id=?"
        );
        return $stmt->execute([
            trim($data['nom']),
            trim($data['prenom']),
            trim($data['mail']),
            trim($data['password']),
            $data['role'] ?? 'user',
            $id
        ]);
    }

    // Delete user
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM user WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Count by role
    public function countByRole($role) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM user WHERE role = ?");
        $stmt->execute([$role]);
        return $stmt->fetchColumn();
    }
}
