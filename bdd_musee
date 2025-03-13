-- Création de la base de données du musée
CREATE DATABASE IF NOT EXISTS musee_db;
USE musee_db;

-- Table des expositions
CREATE TABLE exposition (
    id_exposition INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL,
    descriptif TEXT
);

-- Table des visiteurs
CREATE TABLE visiteur (
    id_visiteur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    age INT,
    tel VARCHAR(20),
    mail VARCHAR(100)
);

-- Table des types de tickets
CREATE TABLE type_ticket (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- Table des visites (relation visiteur-exposition)
CREATE TABLE visiter (
    id_visiteur INT,
    id_exposition INT,
    h_arrivee DATETIME NOT NULL,
    h_depart DATETIME,
    PRIMARY KEY (id_visiteur, id_exposition, h_arrivee),
    FOREIGN KEY (id_visiteur) REFERENCES visiteur(id_visiteur),
    FOREIGN KEY (id_exposition) REFERENCES exposition(id_exposition)
);

-- Table permettant de savoir quel type de ticket permet d'accéder à quelle exposition
CREATE TABLE permettre (
    id_ticket INT,
    id_exposition INT,
    PRIMARY KEY (id_ticket, id_exposition),
    FOREIGN KEY (id_ticket) REFERENCES type_ticket(id_ticket),
    FOREIGN KEY (id_exposition) REFERENCES exposition(id_exposition)
);

-- Table des achats de tickets par les visiteurs
CREATE TABLE acheter (
    id_visiteur INT,
    id_ticket INT,
    date_achat DATETIME NOT NULL,
    PRIMARY KEY (id_visiteur, id_ticket, date_achat),
    FOREIGN KEY (id_visiteur) REFERENCES visiteur(id_visiteur),
    FOREIGN KEY (id_ticket) REFERENCES type_ticket(id_ticket)
);

-- Insertion des données initiales
INSERT INTO exposition (libelle, descriptif) VALUES 
('Exposition Permanente', 'Collection permanente du musée'),
('Exposition Temporaire', 'Exposition spéciale limitée dans le temps');

INSERT INTO type_ticket (libelle) VALUES 
('Exposition Permanente'),
('Exposition Temporaire'),
('Les deux expositions');

-- Association des tickets aux expositions
INSERT INTO permettre (id_ticket, id_exposition) VALUES 
(1, 1), -- Ticket permanent permet d'accéder à l'expo permanente
(2, 2), -- Ticket temporaire permet d'accéder à l'expo temporaire
(3, 1), -- Ticket combiné permet d'accéder à l'expo permanente
(3, 2); -- Ticket combiné permet d'accéder à l'expo temporaire
