<?php
abstract class Model
{
    /** Connexion PDO partagée entre tous les modèles. */
    private static ?PDO $sharedPdo = null;

    protected PDO $pdo;

    public function __construct()
    {
        if (self::$sharedPdo === null) {
            self::$sharedPdo = $this->connect();
        }
        $this->pdo = self::$sharedPdo;
    }

    private function connect(): PDO
    {
        try {
            $port = defined('DB_PORT') ? DB_PORT : '3306';
            $dsn  = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, $port, DB_NAME, DB_CHARSET);
            return new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('[DB] ' . $e->getMessage());
            http_response_code(500);
            $msg = (defined('APP_ENV') && APP_ENV === 'dev')
                ? 'Erreur de connexion : ' . htmlspecialchars($e->getMessage())
                : 'Service temporairement indisponible.';
            die($msg);
        }
    }

    protected function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    protected function fetch(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    protected function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /** Helpers transactions — utiles pour les batch saves. */
    protected function beginTransaction(): void { $this->pdo->beginTransaction(); }
    protected function commit(): void           { $this->pdo->commit(); }
    protected function rollBack(): void         { if ($this->pdo->inTransaction()) $this->pdo->rollBack(); }
}
