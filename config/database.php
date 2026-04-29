<?php
class Database {
    private static $host = 'localhost';
    private static $port = '3307';
    private static $dbname = 'creatorspace';
    private static $username = 'root';
    private static $password = '';
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO("mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$dbname, self::$username, self::$password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>
