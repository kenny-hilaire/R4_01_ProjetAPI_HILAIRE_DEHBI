CREATE DATABASE IF NOT EXISTS authentification_r401
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

USE authentification_r401;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user'
);
INSERT INTO users (login, mot_de_passe, role) 
VALUES ('admin', 'admin', 'directeur');
INSERT INTO users (login, mot_de_passe, role) 
VALUES ('player', 'admin', 'joueur');