USE musee;

-- Table pour les types de billets
CREATE TABLE Type_Ticket (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL
);

-- Table pour les visiteurs
CREATE TABLE Visiteur (
    id_visiteur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    mail VARCHAR(100) NOT NULL UNIQUE,
    tel VARCHAR(15),
    h_arrivee DATETIME DEFAULT CURRENT_TIMESTAMP,
    h_depart DATETIME
);

ALTER TABLE Exposition ADD COLUMN image VARCHAR(255);

-- Table pour les expositions
CREATE TABLE Exposition (
    id_exposition INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    date_debut DATE,
    date_fin DATE
);

-- Table pour l'association entre visiteurs et expositions
CREATE TABLE Visite (
    id_visite INT AUTO_INCREMENT PRIMARY KEY,
    id_visiteur INT,
    id_exposition INT,
    FOREIGN KEY (id_visiteur) REFERENCES Visiteur(id_visiteur),
    FOREIGN KEY (id_exposition) REFERENCES Exposition(id_exposition),
    date_visite DATETIME DEFAULT CURRENT_TIMESTAMP
);

