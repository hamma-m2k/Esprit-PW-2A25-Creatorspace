CREATE DATABASE IF NOT EXISTS creatorspace;
USE creatorspace;

CREATE TABLE auteur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL
);

CREATE TABLE contrat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    date DATE NOT NULL,
    auteur_id INT NOT NULL,
    FOREIGN KEY (auteur_id) REFERENCES auteur(id) ON DELETE CASCADE
);

INSERT INTO auteur (nom, email) VALUES 
('Alice Dupont', 'alice@example.com'),
('Bob Martin', 'bob@example.com');

INSERT INTO contrat (titre, contenu, date, auteur_id) VALUES 
('Contrat de confidentialité', 'Ceci est un NDA standard. Il interdit de divulguer les informations du projet.', '2024-01-10', 1),
('Contrat de prestation', 'Contrat concernant le développement d\'une application web vitrine.', '2024-02-15', 2);
