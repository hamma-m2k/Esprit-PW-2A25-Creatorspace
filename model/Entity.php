<?php
/**
 * Entity.php — Entités du domaine (User, Demande)
 * Attributs privés, constructeur, getters, setters. Aucune logique SQL.
 */

class User {

    private int    $id;
    private string $nom;
    private string $prenom;
    private string $mail;
    private string $password;
    private string $role;
    private string $typeCompte;
    private string $socialMediaLink;
    private string $createdAt;
    private int    $followers;
    private int    $following;

    public function __construct(
        int    $id         = 0,
        string $nom        = '',
        string $prenom     = '',
        string $mail       = '',
        string $password   = '',
        string $role       = 'user',
        string $typeCompte = 'user',
        string $socialMediaLink = '',
        string $createdAt  = '',
        int    $followers  = 0,
        int    $following  = 0
    ) {
        $this->id         = $id;
        $this->nom        = $nom;
        $this->prenom     = $prenom;
        $this->mail       = $mail;
        $this->password   = $password;
        $this->role       = $role;
        $this->typeCompte = $typeCompte;
        $this->socialMediaLink = $socialMediaLink;
        $this->createdAt  = $createdAt;
        $this->followers  = $followers;
        $this->following  = $following;
    }

    public function getId(): int          { return $this->id; }
    public function getNom(): string      { return $this->nom; }
    public function getPrenom(): string   { return $this->prenom; }
    public function getMail(): string     { return $this->mail; }
    public function getPassword(): string { return $this->password; }
    public function getRole(): string     { return $this->role; }
    public function getTypeCompte(): string { return $this->typeCompte; }
    public function getSocialMediaLink(): string { return $this->socialMediaLink; }
    public function getCreatedAt(): string  { return $this->createdAt; }
    public function getFollowers(): int     { return $this->followers; }
    public function getFollowing(): int     { return $this->following; }

    public function setId(int $id): void             { $this->id = $id; }
    public function setNom(string $nom): void        { $this->nom = $nom; }
    public function setPrenom(string $prenom): void  { $this->prenom = $prenom; }
    public function setMail(string $mail): void      { $this->mail = $mail; }
    public function setPassword(string $p): void     { $this->password = $p; }
    public function setRole(string $role): void      { $this->role = $role; }
    public function setTypeCompte(string $t): void   { $this->typeCompte = $t; }
    public function setSocialMediaLink(string $link): void { $this->socialMediaLink = $link; }
    public function setCreatedAt(string $d): void    { $this->createdAt = $d; }
    public function setFollowers(int $n): void       { $this->followers = $n; }
    public function setFollowing(int $n): void       { $this->following = $n; }

    public static function fromArray(array $row): self {
        return new self(
            (int)($row['id']          ?? 0),
            $row['nom']               ?? '',
            $row['prenom']            ?? '',
            $row['mail']              ?? '',
            $row['password']          ?? '',
            $row['role']              ?? 'user',
            $row['type_compte']       ?? 'user',
            $row['social_media_link'] ?? '',
            $row['created_at']        ?? '',
            (int)($row['followers']   ?? 0),
            (int)($row['following']   ?? 0)
        );
    }

    public function toArray(): array {
        return [
            'id'               => $this->id,
            'nom'              => $this->nom,
            'prenom'           => $this->prenom,
            'mail'             => $this->mail,
            'password'         => $this->password,
            'role'             => $this->role,
            'type_compte'      => $this->typeCompte,
            'social_media_link'=> $this->socialMediaLink,
            'created_at'       => $this->createdAt,
            'followers'        => $this->followers,
            'following'        => $this->following,
        ];
    }
}

class Demande {

    private int    $id;
    private string $nom;
    private string $prenom;
    private string $mail;
    private string $password;
    private string $typeCompte;
    private string $socialMediaLink;
    private string $statut;
    private string $createdAt;

    public function __construct(
        int    $id         = 0,
        string $nom        = '',
        string $prenom     = '',
        string $mail       = '',
        string $password   = '',
        string $typeCompte = 'user',
        string $socialMediaLink = '',
        string $statut     = 'en_attente',
        string $createdAt  = ''
    ) {
        $this->id         = $id;
        $this->nom        = $nom;
        $this->prenom     = $prenom;
        $this->mail       = $mail;
        $this->password   = $password;
        $this->typeCompte = $typeCompte;
        $this->socialMediaLink = $socialMediaLink;
        $this->statut     = $statut;
        $this->createdAt  = $createdAt;
    }

    public function getId(): int            { return $this->id; }
    public function getNom(): string        { return $this->nom; }
    public function getPrenom(): string     { return $this->prenom; }
    public function getMail(): string       { return $this->mail; }
    public function getPassword(): string   { return $this->password; }
    public function getTypeCompte(): string { return $this->typeCompte; }
    public function getSocialMediaLink(): string { return $this->socialMediaLink; }
    public function getStatut(): string     { return $this->statut; }
    public function getCreatedAt(): string  { return $this->createdAt; }

    public function setId(int $id): void             { $this->id = $id; }
    public function setNom(string $nom): void        { $this->nom = $nom; }
    public function setPrenom(string $prenom): void  { $this->prenom = $prenom; }
    public function setMail(string $mail): void      { $this->mail = $mail; }
    public function setPassword(string $p): void     { $this->password = $p; }
    public function setTypeCompte(string $t): void   { $this->typeCompte = $t; }
    public function setSocialMediaLink(string $link): void { $this->socialMediaLink = $link; }
    public function setStatut(string $s): void       { $this->statut = $s; }
    public function setCreatedAt(string $d): void    { $this->createdAt = $d; }

    public static function fromArray(array $row): self {
        return new self(
            (int)($row['id']          ?? 0),
            $row['nom']               ?? '',
            $row['prenom']            ?? '',
            $row['mail']              ?? '',
            $row['password']          ?? '',
            $row['type_compte']       ?? 'user',
            $row['social_media_link'] ?? '',
            $row['statut']            ?? 'en_attente',
            $row['created_at']        ?? ''
        );
    }

    public function toArray(): array {
        return [
            'id'          => $this->id,
            'nom'         => $this->nom,
            'prenom'      => $this->prenom,
            'mail'        => $this->mail,
            'password'    => $this->password,
            'type_compte' => $this->typeCompte,
            'social_media_link' => $this->socialMediaLink,
            'statut'      => $this->statut,
            'created_at'  => $this->createdAt,
        ];
    }
}
