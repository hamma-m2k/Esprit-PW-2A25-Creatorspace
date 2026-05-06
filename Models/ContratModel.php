<?php
require_once ROOT . '/Controllers/Model.php';

/**
 * Entité Contrat — propriétés privées + constructeur + getters/setters.
 */
class Contrat
{
    private ?int $id = null;
    private string $titre = '';
    private string $description = '';
    private string $type = 'CDI';
    private ?string $signature = null;
    private ?int $signed_by = null;
    private string $statut = 'brouillon';
    private ?int $created_by = null;
    private ?string $created_at = null;

    public function __construct(array $row = [])
    {
        foreach ($row as $k => $v) {
            if (property_exists($this, $k)) $this->$k = $v;
        }
    }

    public function getId(): ?int             { return $this->id; }
    public function setId(int $v): void       { $this->id = $v; }
    public function getTitre(): string        { return $this->titre; }
    public function setTitre(string $v): void { $this->titre = $v; }
    public function getDescription(): string  { return $this->description; }
    public function setDescription(string $v): void { $this->description = $v; }
    public function getType(): string         { return $this->type; }
    public function setType(string $v): void  { $this->type = $v; }
    public function getSignature(): ?string   { return $this->signature; }
    public function setSignature(?string $v): void  { $this->signature = $v; }
    public function getSignedBy(): ?int       { return $this->signed_by; }
    public function setSignedBy(?int $v): void{ $this->signed_by = $v; }
    public function getStatut(): string       { return $this->statut; }
    public function setStatut(string $v): void{ $this->statut = $v; }
    public function getCreatedBy(): ?int      { return $this->created_by; }
    public function setCreatedBy(?int $v): void { $this->created_by = $v; }
    public function getCreatedAt(): ?string   { return $this->created_at; }
}

class ContratModel extends Model {

    /* ───── Lecture ───── */

    public function getAll(): array {
        return $this->fetchAll(
            'SELECT c.*,
                    u.firstname AS signataire_prenom,
                    u.lastname  AS signataire_nom,
                    cu.firstname AS createur_prenom,
                    cu.lastname  AS createur_nom,
                    (SELECT COUNT(*) FROM rules r WHERE r.contrat_id = c.id) AS nb_rules
             FROM contrats c
             LEFT JOIN users u  ON c.signed_by  = u.id
             LEFT JOIN users cu ON c.created_by = cu.id
             ORDER BY c.created_at DESC'
        );
    }

    /** Liste filtrée : seulement les contrats créés par l'utilisateur. */
    public function getByOwner(int $userId): array {
        return $this->fetchAll(
            'SELECT c.*,
                    u.firstname AS signataire_prenom,
                    u.lastname  AS signataire_nom,
                    cu.firstname AS createur_prenom,
                    cu.lastname  AS createur_nom,
                    (SELECT COUNT(*) FROM rules r WHERE r.contrat_id = c.id) AS nb_rules
             FROM contrats c
             LEFT JOIN users u  ON c.signed_by  = u.id
             LEFT JOIN users cu ON c.created_by = cu.id
             WHERE c.created_by = ?
             ORDER BY c.created_at DESC',
            [$userId]
        );
    }

    public function findById(int $id): array|false {
        return $this->fetch(
            'SELECT c.*,
                    u.firstname AS signataire_prenom,
                    u.lastname  AS signataire_nom,
                    u.email     AS signataire_email
             FROM contrats c
             LEFT JOIN users u ON c.signed_by = u.id
             WHERE c.id = ?',
            [$id]
        );
    }

    public function getRulesForContrat(int $contratId): array {
        return $this->fetchAll(
            'SELECT r.*, u.firstname, u.lastname
             FROM rules r
             LEFT JOIN users u ON r.created_by = u.id
             WHERE r.contrat_id = ?
             ORDER BY r.position ASC, r.id ASC',
            [$contratId]
        );
    }

    /* ───── Écriture ───── */

    public function create(array $d): int {
        $this->query(
            'INSERT INTO contrats (titre, description, type, signature, signed_by, statut, created_by, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
            [
                $d['titre'],
                $d['description'] ?? '',
                $d['type'],
                $d['signature']   ?? null,
                $d['signed_by']   ?? null,
                $d['statut']      ?? 'brouillon',
                $d['created_by']  ?? null,
            ]
        );
        return (int) $this->lastInsertId();
    }

    public function update(int $id, array $d): void {
        $this->query(
            'UPDATE contrats
             SET titre=?, description=?, type=?, signature=?, signed_by=?, statut=?
             WHERE id=?',
            [
                $d['titre'],
                $d['description'] ?? '',
                $d['type'],
                $d['signature']   ?? null,
                $d['signed_by']   ?? null,
                $d['statut']      ?? 'brouillon',
                $id,
            ]
        );
    }

    public function delete(int $id): void {
        $this->query('DELETE FROM contrats WHERE id = ?', [$id]);
    }

    public function updateStatut(int $id, string $statut): void {
        $this->query('UPDATE contrats SET statut=? WHERE id=?', [$statut, $id]);
    }

    /* ───── Stats ───── */

    public function countAll(): int {
        return (int) $this->fetch('SELECT COUNT(*) AS c FROM contrats')['c'];
    }

    public function countByType(string $type): int {
        return (int) $this->fetch(
            'SELECT COUNT(*) AS c FROM contrats WHERE type = ?', [$type]
        )['c'];
    }

    public function countByStatut(string $statut): int {
        return (int) $this->fetch(
            'SELECT COUNT(*) AS c FROM contrats WHERE statut = ?', [$statut]
        )['c'];
    }

    /* ───── Validation PHP (pas HTML5) ───── */

    public function validate(array $d): array {
        $errors = [];

        if (empty(trim($d['titre'] ?? ''))) {
            $errors['titre'] = 'Le titre est obligatoire.';
        } elseif (strlen(trim($d['titre'])) < 3) {
            $errors['titre'] = 'Le titre doit contenir au moins 3 caractères.';
        } elseif (strlen(trim($d['titre'])) > 200) {
            $errors['titre'] = 'Le titre ne peut pas dépasser 200 caractères.';
        }

        $typesAutorisés = ['CDI', 'CDD', 'CDIV'];
        if (empty($d['type']) || !in_array($d['type'], $typesAutorisés, true)) {
            $errors['type'] = 'Le type doit être CDI, CDD ou CDIV.';
        }

        if (!empty($d['description']) && strlen($d['description']) > 5000) {
            $errors['description'] = 'La description ne peut pas dépasser 5000 caractères.';
        }

        return $errors;
    }
}
