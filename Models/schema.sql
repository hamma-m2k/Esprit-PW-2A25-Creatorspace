-- ============================================================
--  AntiGravity / CreatorSpace — Base de données complète
--  PDO requis | MVC | POO
-- ============================================================

CREATE DATABASE IF NOT EXISTS creatorspeace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE creatorspeace;

-- -------------------------
-- Rôles
-- -------------------------
CREATE TABLE IF NOT EXISTS roles (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(60)  NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT '',
    created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO roles (name, description) VALUES
('superadmin', 'Accès total'),
('admin',      'Administration'),
('user',       'Utilisateur standard');

-- -------------------------
-- Permissions
-- -------------------------
CREATE TABLE IF NOT EXISTS permissions (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(100) NOT NULL UNIQUE,
    module VARCHAR(60)  NOT NULL
);

INSERT IGNORE INTO permissions (name, module) VALUES
('view_users',    'users'),
('create_users',  'users'),
('edit_users',    'users'),
('delete_users',  'users'),
('view_contrats', 'contrats'),
('create_contrats','contrats'),
('edit_contrats', 'contrats'),
('delete_contrats','contrats'),
('view_rules',    'rules'),
('create_rules',  'rules'),
('edit_rules',    'rules'),
('delete_rules',  'rules');

-- -------------------------
-- Role ↔ Permissions (jointure)
-- -------------------------
CREATE TABLE IF NOT EXISTS role_permissions (
    role_id       INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id)       REFERENCES roles(id)       ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- -------------------------
-- Utilisateurs
-- -------------------------
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    firstname  VARCHAR(80)  NOT NULL,
    lastname   VARCHAR(80)  NOT NULL,
    email      VARCHAR(180) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role_id    INT          DEFAULT 3,
    status     ENUM('active','inactive','banned') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);

-- Compte admin par défaut  (mot de passe : Admin1234!)
INSERT IGNORE INTO users (firstname, lastname, email, password, role_id, status)
VALUES ('Super', 'Admin', 'admin@creatorspeace.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active');

-- -------------------------
-- Demandes d'inscription
-- -------------------------
CREATE TABLE IF NOT EXISTS registration_requests (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    firstname    VARCHAR(80)  NOT NULL,
    lastname     VARCHAR(80)  NOT NULL,
    email        VARCHAR(180) NOT NULL,
    account_type VARCHAR(60)  DEFAULT 'standard',
    message      TEXT         DEFAULT NULL,
    status       ENUM('pending','approved','rejected') DEFAULT 'pending',
    reviewed_by  INT          DEFAULT NULL,
    reviewed_at  DATETIME     DEFAULT NULL,
    created_at   DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- -------------------------
-- CONTRATS  (Entité 1)
-- -------------------------
CREATE TABLE IF NOT EXISTS contrats (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    titre       VARCHAR(200) NOT NULL,
    description TEXT         DEFAULT NULL,
    type        ENUM('CDI','CDD','CDIV') NOT NULL DEFAULT 'CDI',
    signature   VARCHAR(255) DEFAULT NULL,   -- nom ou URL de la signature
    signed_by   INT          DEFAULT NULL,   -- FK vers users
    statut      ENUM('brouillon','actif','archive') DEFAULT 'brouillon',
    created_by  INT          DEFAULT NULL,
    created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (signed_by)  REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- -------------------------
-- RULES  (Entité 2)
-- -------------------------
CREATE TABLE IF NOT EXISTS rules (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    contrat_id  INT          NOT NULL,         -- clé étrangère → contrats
    titre       VARCHAR(200) NOT NULL,
    description TEXT         DEFAULT NULL,
    position    INT          DEFAULT 0,        -- ordre d'affichage
    source      ENUM('manuel','import') DEFAULT 'manuel',
    created_by  INT          DEFAULT NULL,
    created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contrat_id) REFERENCES contrats(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)    ON DELETE SET NULL
);

-- -------------------------
-- Historique
-- -------------------------
CREATE TABLE IF NOT EXISTS activity_logs (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          DEFAULT NULL,
    action     VARCHAR(100) NOT NULL,
    details    TEXT         DEFAULT NULL,
    ip_address VARCHAR(45)  DEFAULT NULL,
    created_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- -------------------------
-- Vue SQL : contrats avec leurs règles (JOIN)
-- -------------------------
CREATE OR REPLACE VIEW v_contrats_rules AS
    SELECT
        c.id            AS contrat_id,
        c.titre         AS contrat_titre,
        c.type          AS contrat_type,
        c.statut        AS contrat_statut,
        c.created_at    AS contrat_date,
        u.firstname     AS signataire_prenom,
        u.lastname      AS signataire_nom,
        COUNT(r.id)     AS nb_rules
    FROM contrats c
    LEFT JOIN users u  ON c.signed_by  = u.id
    LEFT JOIN rules r  ON r.contrat_id = c.id
    GROUP BY c.id;
