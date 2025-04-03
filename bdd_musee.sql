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
    FOREIGN KEY (id_ticket) REFERENCES Exposition(id_exposition) ON DELETE CASCADE,
    FOREIGN KEY (id_ticket) REFERENCES Visiteur(id_visiteur) ON DELETE CASCADE
);

 -- Table Visite ( entre Exposition et Visiteurs)
CREATE TABLE Visite (
    id_exposition INT,
    id_visiteur INT,
    PRIMARY KEY (id_exposition, id_visiteur)
    FOREIGN KEY (id_exposition ) REFERENCES Visiteur(id_visiteur) ON DELETE CASCADE
);