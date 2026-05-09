<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=creatorspace;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Modify the statut column to include new ENUM values
    $pdo->exec("
        ALTER TABLE contrats 
        MODIFY statut ENUM('brouillon', 'en_attente', 'accepte', 'refuse', 'actif', 'archive') DEFAULT 'en_attente'
    ");

    echo "Table 'contrats' modifiee avec succes !";
} catch (PDOException $e) {
    echo "Erreur lors de la modification : " . $e->getMessage();
}
