<?php
/**
 * Entity.php — Modèles (Data Transfer Objects / Entités)
 * Contient uniquement : attributs, constructeur, getters et setters.
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
    private bool   $isAccepted;
    private bool   $isVerified;
    private bool   $isBanned;
    private string $profilePicture;
    private bool   $twoFactorEnabled;
    private ?string $twoFactorCode;

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
        int    $following  = 0,
        bool   $isAccepted = false,
        bool   $isVerified = false,
        bool   $isBanned   = false,
        string $profilePicture = '',
        bool   $twoFactorEnabled = false,
        ?string $twoFactorCode = null
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
        $this->isAccepted = $isAccepted;
        $this->isVerified = $isVerified;
        $this->isBanned   = $isBanned;
        $this->profilePicture = $profilePicture;
        $this->twoFactorEnabled = $twoFactorEnabled;
        $this->twoFactorCode = $twoFactorCode;
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
    public function getIsAccepted(): bool   { return $this->isAccepted; }
    public function getIsVerified(): bool   { return $this->isVerified; }
    public function getIsBanned(): bool     { return $this->isBanned; }
    public function getProfilePicture(): string { return $this->profilePicture; }
    public function getTwoFactorEnabled(): bool { return $this->twoFactorEnabled; }
    public function getTwoFactorCode(): ?string { return $this->twoFactorCode; }

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
    public function setIsAccepted(bool $b): void     { $this->isAccepted = $b; }
    public function setIsVerified(bool $b): void     { $this->isVerified = $b; }
    public function setIsBanned(bool $b): void       { $this->isBanned = $b; }
    public function setProfilePicture(string $p): void { $this->profilePicture = $p; }
    public function setTwoFactorEnabled(bool $b): void { $this->twoFactorEnabled = $b; }
    public function setTwoFactorCode(?string $c): void { $this->twoFactorCode = $c; }
}

/**
 * SessionManager — Gère la session utilisateur.
 */
class SessionManager
{
    public static function setUser(array $user): void
    {
        $_SESSION['user'] = $user;
    }

    public static function getUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function destroy(): void
    {
        unset($_SESSION['user']);
        session_destroy();
    }

    public static function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    public static function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}

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
