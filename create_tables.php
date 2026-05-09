<?php
// Script to create the necessary tables for Contrats and Rules
try {
    $pdo = new PDO('mysql:host=localhost;dbname=creatorspace;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Contrats table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contrats (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            titre       VARCHAR(200) NOT NULL,
            description TEXT         DEFAULT NULL,
            type        ENUM('CDI','CDD','CDIV') NOT NULL DEFAULT 'CDI',
            signature   VARCHAR(255) DEFAULT NULL,
            signed_by   INT          DEFAULT NULL,
            statut      ENUM('brouillon','actif','archive') DEFAULT 'brouillon',
            created_by  INT          DEFAULT NULL,
            created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP,
            updated_at  DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (signed_by)  REFERENCES user(id) ON DELETE SET NULL,
            FOREIGN KEY (created_by) REFERENCES user(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Create Rules table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rules (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            contrat_id  INT          NOT NULL,
            titre       VARCHAR(200) NOT NULL,
            description TEXT         DEFAULT NULL,
            position    INT          DEFAULT 0,
            source      ENUM('manuel','import') DEFAULT 'manuel',
            created_by  INT          DEFAULT NULL,
            created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (contrat_id) REFERENCES contrats(id) ON DELETE CASCADE,
            FOREIGN KEY (created_by) REFERENCES user(id)    ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "Tables 'contrats' et 'rules' crees avec succes !";
} catch (PDOException $e) {
    echo "Erreur lors de la creation des tables : " . $e->getMessage();
}
