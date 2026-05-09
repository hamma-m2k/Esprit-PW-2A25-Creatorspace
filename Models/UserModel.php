<?php
require_once ROOT . '/Controllers/Model.php';

/**
 * Entité User — propriétés privées + constructeur + getters/setters.
 */
class User
{
    private ?int $id = null;
    private string $firstname = '';
    private string $lastname  = '';
    private string $email     = '';
    private ?string $password = null;
    private int $role_id      = 3;
    private string $status    = 'active';
    private ?string $created_at = null;

    public function __construct(array $row = [])
    {
        foreach ($row as $k => $v) {
            if (property_exists($this, $k)) $this->$k = $v;
        }
    }

    public function getId(): ?int          { return $this->id; }
    public function setId(int $v): void    { $this->id = $v; }
    public function getFirstname(): string { return $this->firstname; }
    public function setFirstname(string $v): void { $this->firstname = $v; }
    public function getLastname(): string  { return $this->lastname; }
    public function setLastname(string $v): void  { $this->lastname = $v; }
    public function getEmail(): string     { return $this->email; }
    public function setEmail(string $v): void     { $this->email = $v; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $v): void  { $this->password = $v; }
    public function getRoleId(): int       { return $this->role_id; }
    public function setRoleId(int $v): void       { $this->role_id = $v; }
    public function getStatus(): string    { return $this->status; }
    public function setStatus(string $v): void    { $this->status = $v; }
    public function getCreatedAt(): ?string{ return $this->created_at; }
    public function fullName(): string     { return trim($this->firstname . ' ' . $this->lastname); }
}

/**
 * Repository — accès BDD pour User. Conserve la compat avec le code existant.
 */
class UserModel extends Model {

    public function findByEmail(string $email): array|false {
        return $this->fetch(
            'SELECT u.*, r.name AS role_name
             FROM users u
             LEFT JOIN roles r ON u.role_id = r.id
             WHERE u.email = ?',
            [$email]
        );
    }

    public function findById(int $id): array|false {
        return $this->fetch(
            'SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = ?',
            [$id]
        );
    }

    public function getAll(): array {
        return $this->fetchAll(
            'SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC'
        );
    }

    public function create(array $data): string {
        $this->query(
            'INSERT INTO users (firstname, lastname, email, password, role_id, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['firstname'],
                $data['lastname'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['role_id'] ?? 3,
                $data['status'] ?? 'active',
            ]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $this->query(
            'UPDATE users SET firstname=?, lastname=?, email=?, role_id=?, status=? WHERE id=?',
            [$data['firstname'], $data['lastname'], $data['email'], $data['role_id'], $data['status'], $id]
        );
    }

    public function delete(int $id): void {
        $this->query('DELETE FROM users WHERE id = ?', [$id]);
    }

    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public function countAll(): int {
        return (int) $this->fetch('SELECT COUNT(*) as c FROM users')['c'];
    }

    public function countByStatus(string $status): int {
        return (int) $this->fetch('SELECT COUNT(*) as c FROM users WHERE status = ?', [$status])['c'];
    }
}
