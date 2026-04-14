<?php
// /model/ProduitModel.php

require_once __DIR__ . '/config.php';

class ProduitModel {
    private $db;

    public function __construct() {
        global $host, $dbname, $user, $password;
        $this->db = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $user,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM produits ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM produits WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert(array $data) {
        $stmt = $this->db->prepare(
            "INSERT INTO produits (nom, description, prix, stock)
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([
            trim($data['nom']),
            trim($data['description']),
            (float)$data['prix'],
            (int)$data['stock']
        ]);
    }

    public function update($id, array $data) {
        $stmt = $this->db->prepare(
            "UPDATE produits
             SET nom=?, description=?, prix=?, stock=?
             WHERE id=?"
        );
        return $stmt->execute([
            trim($data['nom']),
            trim($data['description']),
            (float)$data['prix'],
            (int)$data['stock'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM produits WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
