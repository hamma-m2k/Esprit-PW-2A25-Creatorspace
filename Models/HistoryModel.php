<?php
require_once ROOT . '/Controllers/Model.php';

/** Entité History. */
class History
{
    private ?int $id = null;
    private ?int $user_id = null;
    private string $action = '';
    private ?string $details = null;
    private ?string $created_at = null;

    public function __construct(array $row = [])
    {
        foreach ($row as $k => $v) {
            if (property_exists($this, $k)) $this->$k = $v;
        }
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $v): void { $this->id = $v; }
    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $v): void { $this->user_id = $v; }
    public function getAction(): string { return $this->action; }
    public function setAction(string $v): void { $this->action = $v; }
    public function getDetails(): ?string { return $this->details; }
    public function setDetails(?string $v): void { $this->details = $v; }
    public function getCreatedAt(): ?string { return $this->created_at; }
}

class HistoryModel extends Model {

    public function log(int $userId, string $action, string $details = ''): void {
        $this->query(
            'INSERT INTO activity_logs (user_id, action, details, ip_address, created_at)
             VALUES (?, ?, ?, ?, NOW())',
            [$userId, $action, $details, $_SERVER['REMOTE_ADDR'] ?? 'unknown']
        );
    }

    public function getAll(int $limit = 100): array {
        return $this->fetchAll(
            'SELECT l.*, u.firstname, u.lastname, u.email
             FROM activity_logs l
             LEFT JOIN users u ON l.user_id = u.id
             ORDER BY l.created_at DESC
             LIMIT ' . (int)$limit
        );
    }

    public function getByUser(int $userId): array {
        return $this->fetchAll(
            'SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 50',
            [$userId]
        );
    }
}
