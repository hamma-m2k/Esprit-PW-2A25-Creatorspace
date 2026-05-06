<?php
require_once ROOT . '/Controllers/Model.php';

/** Entité Rule. */
class Rule
{
    private ?int $id = null;
    private ?int $contrat_id = null;
    private string $titre = '';
    private string $description = '';
    private int $position = 0;
    private ?int $created_by = null;
    private ?string $created_at = null;

    public function __construct(array $row = [])
    {
        foreach ($row as $k => $v) {
            if (property_exists($this, $k)) $this->$k = $v;
        }
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $v): void { $this->id = $v; }
    public function getContratId(): ?int { return $this->contrat_id; }
    public function setContratId(?int $v): void { $this->contrat_id = $v; }
    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $v): void { $this->titre = $v; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $v): void { $this->description = $v; }
    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): void { $this->position = $v; }
    public function getCreatedBy(): ?int { return $this->created_by; }
    public function setCreatedBy(?int $v): void { $this->created_by = $v; }
    public function getCreatedAt(): ?string { return $this->created_at; }
}

class RuleModel extends Model {

    /* ───── Lecture ───── */

    public function getAll(): array {
        // Jointure rules ↔ contrats
        return $this->fetchAll(
            'SELECT r.*,
                    c.titre  AS contrat_titre,
                    c.type   AS contrat_type,
                    u.firstname AS auteur_prenom,
                    u.lastname  AS auteur_nom
             FROM rules r
             INNER JOIN contrats c ON r.contrat_id = c.id
             LEFT  JOIN users    u ON r.created_by  = u.id
             ORDER BY r.contrat_id ASC, r.position ASC'
        );
    }

    public function getByContrat(int $contratId): array {
        return $this->fetchAll(
            'SELECT r.*,
                    c.titre AS contrat_titre,
                    u.firstname AS auteur_prenom,
                    u.lastname  AS auteur_nom
             FROM rules r
             INNER JOIN contrats c ON r.contrat_id = c.id
             LEFT  JOIN users    u ON r.created_by  = u.id
             WHERE r.contrat_id = ?
             ORDER BY r.position ASC, r.id ASC',
            [$contratId]
        );
    }

    public function findById(int $id): array|false {
        return $this->fetch(
            'SELECT r.*,
                    c.titre AS contrat_titre,
                    c.type  AS contrat_type
             FROM rules r
             INNER JOIN contrats c ON r.contrat_id = c.id
             WHERE r.id = ?',
            [$id]
        );
    }

    /* ───── Écriture ───── */

    public function create(array $d): int {
        // Récupérer la prochaine position pour ce contrat
        $maxPos = $this->fetch(
            'SELECT COALESCE(MAX(position), 0) AS m FROM rules WHERE contrat_id = ?',
            [(int)$d['contrat_id']]
        )['m'] ?? 0;

        $this->query(
            'INSERT INTO rules (contrat_id, titre, description, position, source, created_by, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())',
            [
                (int) $d['contrat_id'],
                $d['titre'],
                $d['description'] ?? '',
                (int)($d['position'] ?? $maxPos + 1),
                $d['source']      ?? 'manuel',
                $d['created_by']  ?? null,
            ]
        );
        return (int) $this->lastInsertId();
    }

    public function importBatch(int $contratId, array $rules, int $userId): int {
        $maxPos = (int)($this->fetch(
            'SELECT COALESCE(MAX(position), 0) AS m FROM rules WHERE contrat_id = ?',
            [$contratId]
        )['m'] ?? 0);

        $inserted = 0;
        $this->beginTransaction();
        try {
            foreach ($rules as $i => $rule) {
                if (!is_array($rule)) continue;
                $titre = trim((string)($rule['titre'] ?? ''));
                if ($titre === '' || strlen($titre) > 200) continue;
                $desc = trim((string)($rule['description'] ?? ''));
                if (strlen($desc) > 5000) $desc = substr($desc, 0, 5000);

                $this->query(
                    'INSERT INTO rules (contrat_id, titre, description, position, source, created_by, created_at)
                     VALUES (?, ?, ?, ?, "import", ?, NOW())',
                    [$contratId, $titre, $desc, $maxPos + $i + 1, $userId]
                );
                $inserted++;
            }
            $this->commit();
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
        return $inserted;
    }

    public function update(int $id, array $d): void {
        $this->query(
            'UPDATE rules SET titre=?, description=?, position=? WHERE id=?',
            [$d['titre'], $d['description'] ?? '', (int)($d['position'] ?? 0), $id]
        );
    }

    public function delete(int $id): void {
        $this->query('DELETE FROM rules WHERE id = ?', [$id]);
    }

    public function reorder(int $contratId, array $orderedIds): void {
        foreach ($orderedIds as $pos => $ruleId) {
            $this->query(
                'UPDATE rules SET position=? WHERE id=? AND contrat_id=?',
                [$pos + 1, (int)$ruleId, $contratId]
            );
        }
    }

    /* ───── Stats ───── */

    public function countAll(): int {
        return (int) $this->fetch('SELECT COUNT(*) AS c FROM rules')['c'];
    }

    public function countByContrat(int $contratId): int {
        return (int) $this->fetch(
            'SELECT COUNT(*) AS c FROM rules WHERE contrat_id = ?', [$contratId]
        )['c'];
    }

    /* ───── Validation PHP (pas HTML5) ───── */

    public function validate(array $d): array {
        $errors = [];

        if (empty(trim($d['titre'] ?? ''))) {
            $errors['titre'] = 'Le titre de la règle est obligatoire.';
        } elseif (strlen(trim($d['titre'])) < 2) {
            $errors['titre'] = 'Le titre doit contenir au moins 2 caractères.';
        } elseif (strlen(trim($d['titre'])) > 200) {
            $errors['titre'] = 'Le titre ne peut pas dépasser 200 caractères.';
        }

        if (empty($d['contrat_id']) || (int)$d['contrat_id'] <= 0) {
            $errors['contrat_id'] = 'Un contrat doit être sélectionné.';
        }

        if (isset($d['position']) && $d['position'] !== '') {
            if (!ctype_digit((string)$d['position'])) {
                $errors['position'] = 'La position doit être un entier positif.';
            }
        }

        return $errors;
    }
}
