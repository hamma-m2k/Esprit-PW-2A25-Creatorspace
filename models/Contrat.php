<?php
require_once 'config/database.php';

class Contrat {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function getAll() {
        // Jointure avec la table auteur
        $stmt = $this->pdo->prepare("SELECT c.*, a.nom as auteur_nom FROM contrat c JOIN auteur a ON c.auteur_id = a.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT c.*, a.nom as auteur_nom FROM contrat c JOIN auteur a ON c.auteur_id = a.id WHERE c.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($titre, $contenu, $date, $auteur_id) {
        $stmt = $this->pdo->prepare("INSERT INTO contrat (titre, contenu, date, auteur_id) VALUES (:titre, :contenu, :date, :auteur_id)");
        return $stmt->execute([
            'titre' => $titre,
            'contenu' => $contenu,
            'date' => $date,
            'auteur_id' => $auteur_id
        ]);
    }

    public function update($id, $titre, $contenu, $date, $auteur_id) {
        $stmt = $this->pdo->prepare("UPDATE contrat SET titre = :titre, contenu = :contenu, date = :date, auteur_id = :auteur_id WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'titre' => $titre,
            'contenu' => $contenu,
            'date' => $date,
            'auteur_id' => $auteur_id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM contrat WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>
