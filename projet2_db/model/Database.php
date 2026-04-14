<?php
require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;

    private function __construct() {}

    public static function getInstance() {
        global $host, $dbname, $user, $password;
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8",
                    $user,
                    $password,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                die("Erreur connexion : " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
