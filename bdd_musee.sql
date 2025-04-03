-- Création de la base de données
CREATE DATABASE IF NOT EXISTS musee_db;
USE musee_db;

-- Table Exposition
CREATE TABLE Exposition (
    id_exposition INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL,
    descriptif TEXT NOT NULL
);

-- Table Visiteur
CREATE TABLE Visiteur (
    id_visiteur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    mail VARCHAR(100) NOT NULL,
    tel VARCHAR(20) NOT NULL,
    h_depart DATETIME,
    h_arrivee DATETIME NOT NULL
);

-- Table Type_Ticket
CREATE TABLE Type_Ticket (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- Table Permettre (association entre Exposition et Type_Ticket)
CREATE TABLE Permettre (
    id_exposition INT,
    id_ticket INT,
    PRIMARY KEY (id_exposition, id_ticket),
    FOREIGN KEY (id_exposition) REFERENCES Exposition(id_exposition) ON DELETE CASCADE,
    FOREIGN KEY (id_ticket) REFERENCES Type_Ticket(id_ticket) ON DELETE CASCADE
);

-- Table Acheter (association entre Visiteur et Type_Ticket)
CREATE TABLE Acheter (
    id_visiteur INT,
    id_ticket INT,
    PRIMARY KEY (id_visiteur, id_ticket),
    FOREIGN KEY (id_visiteur) REFERENCES Visiteur(id_visiteur) ON DELETE CASCADE,
    FOREIGN KEY (id_ticket) REFERENCES Type_Ticket(id_ticket) ON DELETE CASCADE
);

-- Insertion de données initiales pour tester
INSERT INTO Exposition (libelle, descriptif) VALUES 
('Exposition Permanente', 'Collection permanente du musée'),
('Exposition Temporaire', 'Exposition spéciale limitée dans le temps');

INSERT INTO Type_Ticket (libelle) VALUES 
('Exposition Permanente'),
('Exposition Temporaire'),
('Les deux expositions');

INSERT INTO Visiteur (nom, prenom, age, mail, tel, h_depart, h_arrivee) VALUES
('Dupont', 'Jean', 30, 'jean.dupont@example.com', '0601020304', '2023-10-01 15:00:00', '2023-10-01 13:00:00');

INSERT INTO Acheter (id_visiteur, id_ticket) VALUES
(1, 1),
(1, 2);

INSERT INTO Permettre (id_exposition, id_ticket) VALUES
(1, 1),
(2, 2),
(1, 3),
(2, 3);


ALTER TABLE Visiteur ADD COLUMN h_arrivee DATETIME;
DESCRIBE Visiteur;