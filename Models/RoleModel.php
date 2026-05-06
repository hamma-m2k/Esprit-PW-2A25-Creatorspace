<?php
require_once ROOT . '/Controllers/Model.php';

/** Entité Role. */
class Role
{
    private ?int $id = null;
    private string $name = '';
    private ?string $description = null;

    public function __construct(array $row = [])
    {
        foreach ($row as $k => $v) {
            if (property_exists($this, $k)) $this->$k = $v;
        }
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $v): void { $this->id = $v; }
    public function getName(): string { return $this->name; }
    public function setName(string $v): void { $this->name = $v; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $v): void { $this->description = $v; }
}

class RoleModel extends Model {

    public function getAll(): array {
        return $this->fetchAll('SELECT * FROM roles ORDER BY id ASC');
    }

    public function findById(int $id): array|false {
        return $this->fetch('SELECT * FROM roles WHERE id = ?', [$id]);
    }

    public function create(string $name, string $description): string {
        $this->query(
            'INSERT INTO roles (name, description, created_at) VALUES (?, ?, NOW())',
            [$name, $description]
        );
        return $this->lastInsertId();
    }

    public function delete(int $id): void {
        $this->query('DELETE FROM roles WHERE id = ?', [$id]);
    }

    public function getPermissions(int $roleId): array {
        return $this->fetchAll(
            'SELECT p.* FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = ?',
            [$roleId]
        );
    }

    public function getAllPermissions(): array {
        return $this->fetchAll('SELECT * FROM permissions ORDER BY module, name');
    }

    public function setPermissions(int $roleId, array $permissionIds): void {
        $this->query('DELETE FROM role_permissions WHERE role_id = ?', [$roleId]);
        foreach ($permissionIds as $pid) {
            $this->query(
                'INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)',
                [$roleId, (int)$pid]
            );
        }
    }
}
