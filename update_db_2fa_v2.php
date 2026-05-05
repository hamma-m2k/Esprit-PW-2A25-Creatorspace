<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=creatorspace;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if columns exist first
    $stmt = $pdo->query("SHOW COLUMNS FROM user LIKE 'two_factor_enabled'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE user ADD COLUMN two_factor_enabled TINYINT(1) DEFAULT 0");
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM user LIKE 'two_factor_code'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE user ADD COLUMN two_factor_code VARCHAR(10) DEFAULT NULL");
    }
    
    echo "Database updated successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
