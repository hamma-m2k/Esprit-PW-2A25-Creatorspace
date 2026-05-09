<?php
require_once ROOT . '/Controllers/Model.php';

/** Entité Request (demande d'inscription). */
class Request
{
    private ?int $id = null;
    private string $firstname = '';
    private string $lastname  = '';
    private string $email     = '';
    private ?string $message  = null;
    private string $status    = 'pending';
    private ?string $created_at = null;

    public function __construct(array $row = [])
    {
        foreach ($row as $k => $v) {
            if (property_exists($this, $k)) $this->$k = $v;
        }
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $v): void { $this->id = $v; }
    public function getFirstname(): string { return $this->firstname; }
    public function setFirstname(string $v): void { $this->firstname = $v; }
    public function getLastname(): string { return $this->lastname; }
    public function setLastname(string $v): void { $this->lastname = $v; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $v): void { $this->email = $v; }
    public function getMessage(): ?string { return $this->message; }
    public function setMessage(?string $v): void { $this->message = $v; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $v): void { $this->status = $v; }
    public function getCreatedAt(): ?string { return $this->created_at; }
}

class RequestModel extends Model {

    public function getAll(): array {
        return $this->fetchAll(
            'SELECT r.*, u.firstname AS reviewer_firstname, u.lastname AS reviewer_lastname
             FROM registration_requests r
             LEFT JOIN users u ON r.reviewed_by = u.id
             ORDER BY r.created_at DESC'
        );
    }

    public function findById(int $id): array|false {
        return $this->fetch(
            'SELECT r.*, u.firstname AS reviewer_firstname, u.lastname AS reviewer_lastname
             FROM registration_requests r
             LEFT JOIN users u ON r.reviewed_by = u.id
             WHERE r.id = ?',
            [$id]
        );
    }

    public function create(array $data): string {
        $this->query(
            'INSERT INTO registration_requests (firstname, lastname, email, account_type, message, status, created_at)
             VALUES (?, ?, ?, ?, ?, "pending", NOW())',
            [$data['firstname'], $data['lastname'], $data['email'], $data['account_type'], $data['message'] ?? '']
        );
        return $this->lastInsertId();
    }

    public function updateStatus(int $id, string $status, int $reviewerId): void {
        $this->query(
            'UPDATE registration_requests SET status=?, reviewed_by=?, reviewed_at=NOW() WHERE id=?',
            [$status, $reviewerId, $id]
        );
    }

    public function countByStatus(string $status): int {
        return (int) $this->fetch(
            'SELECT COUNT(*) as c FROM registration_requests WHERE status = ?', [$status]
        )['c'];
    }

    public function countAll(): int {
        return (int) $this->fetch('SELECT COUNT(*) as c FROM registration_requests')['c'];
    }
}
