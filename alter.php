<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=creatorspace;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("ALTER TABLE contrats MODIFY COLUMN statut ENUM('en_attente', 'approuve_createur', 'accepte', 'refuse', 'brouillon', 'actif', 'archive') DEFAULT 'en_attente'");
    echo "OK";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
