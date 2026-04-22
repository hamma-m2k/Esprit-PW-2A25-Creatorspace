<?php
/**
 * DemandeModel.php — Couche d'accès aux données pour `demande_inscription`
 * Utilise l'entité Demande (attributs, constructeur, getters/setters)
 * Aucun HTML, aucun echo, aucune logique métier ici.
 */

require_once __DIR__ . '/Entity.php';
require_once __DIR__ . '/UserModel.php';

class DemandeModel {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ── Lecture ───────────────────────────────────────────────

    /** Retourne toutes les demandes en attente */
    public function getEnAttente(): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM demande_inscription
             WHERE statut = 'en_attente'
             ORDER BY created_at ASC"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return array_map(fn($r) => Demande::fromArray($r)->toArray(), $rows);
    }

    /** Retourne toutes les demandes (historique) */
    public function getAll(): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM demande_inscription ORDER BY created_at DESC"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return array_map(fn($r) => Demande::fromArray($r)->toArray(), $rows);
    }

    /** Retourne une demande par son ID */
    public function getById(int $id): ?Demande {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM demande_inscription WHERE id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? Demande::fromArray($row) : null;
    }

    /** Vérifie si un mail a déjà une demande en attente */
    public function mailEnAttente(string $mail): bool {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM demande_inscription
             WHERE mail = ? AND statut = 'en_attente' LIMIT 1"
        );
        $stmt->execute([$mail]);
        return $stmt->fetch() !== false;
    }

    /** Compte les demandes en attente */
    public function countEnAttente(): int {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM demande_inscription WHERE statut = 'en_attente'"
        );
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    // ── Écriture ──────────────────────────────────────────────

    /** Crée une nouvelle demande (password hashé en MD5) */
    public function creerDemande(array $data): int {
        $demande = new Demande(
            0,
            trim($data['nom']         ?? ''),
            trim($data['prenom']      ?? ''),
            trim($data['mail']        ?? ''),
            trim($data['password']    ?? ''),
            trim($data['type_compte'] ?? 'user'),
            trim($data['social_media_link'] ?? ''),
            'en_attente'
        );

        $stmt = $this->pdo->prepare(
            "INSERT INTO demande_inscription
             (nom, prenom, mail, password, type_compte, social_media_link, statut)
             VALUES (?, ?, ?, MD5(?), ?, ?, 'en_attente')"
        );
        $stmt->execute([
            $demande->getNom(),
            $demande->getPrenom(),
            $demande->getMail(),
            $demande->getPassword(),
            $demande->getTypeCompte(),
            $demande->getSocialMediaLink()
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Accepte une demande :
     * - Crée le compte dans la table user (password déjà MD5, pas de double hashage)
     * - Marque la demande comme 'accepte'
     */
    public function accepter(int $id, UserModel $userModel): bool {
        $demande = $this->getById($id);
        if (!$demande) return false;

        // Insère dans user avec le password déjà hashé
        $stmt = $this->pdo->prepare(
            "INSERT INTO `user` (nom, prenom, mail, `password`, role, type_compte, social_media_link)
             VALUES (?, ?, ?, ?, 'user', ?, ?)"
        );
        $stmt->execute([
            $demande->getNom(),
            $demande->getPrenom(),
            $demande->getMail(),
            $demande->getPassword(),   // déjà MD5
            $demande->getTypeCompte(),
            $demande->getSocialMediaLink()
        ]);

        // Marque la demande comme acceptée
        $demande->setStatut('accepte');
        $stmt2 = $this->pdo->prepare(
            "UPDATE demande_inscription SET statut = ? WHERE id = ?"
        );
        $stmt2->execute([$demande->getStatut(), $demande->getId()]);
        return true;
    }

    /** Refuse une demande (garde l'historique) */
    public function refuser(int $id): bool {
        $demande = $this->getById($id);
        if (!$demande) return false;

        $demande->setStatut('refuse');
        $stmt = $this->pdo->prepare(
            "UPDATE demande_inscription SET statut = ? WHERE id = ?"
        );
        $stmt->execute([$demande->getStatut(), $demande->getId()]);
        return true;
    }

    /** Supprime définitivement une demande */
    public function supprimer(int $id): void {
        $stmt = $this->pdo->prepare(
            "DELETE FROM demande_inscription WHERE id = ?"
        );
        $stmt->execute([$id]);
    }
}
