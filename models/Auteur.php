<?php
require_once 'config/database.php';

class Auteur {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM auteur");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
