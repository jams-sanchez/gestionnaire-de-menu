-- Création de la table USER
DROP TABLE IF EXISTS utilisateur;
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mail VARCHAR(191) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Création de la table MENU
DROP TABLE IF EXISTS menu;
CREATE TABLE menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prix NUMERIC(10,2)
);

-- Création de la table PLAT
DROP TABLE IF EXISTS plat;
CREATE TABLE plat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    prix NUMERIC(10,2),
    image VARCHAR(255),
    id_categorie INT REFERENCES categorie(id)
);

-- Table d'association PLAT <-> MENU
DROP TABLE IF EXISTS plat_menu;
CREATE TABLE plat_menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plat_id INT NOT NULL,
    menu_id INT NOT NULL,
    FOREIGN KEY (plat_id) REFERENCES plat(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE
);

-- Création de la table INGREDIENT
DROP TABLE IF EXISTS ingredient;
CREATE TABLE ingredient (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

-- Table d'association INGREDIENT <-> PLAT
DROP TABLE IF EXISTS ingredient_plat;
CREATE TABLE ingredient_plat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingredient_id INT NOT NULL,
    plat_id INT NOT NULL,
    FOREIGN KEY (ingredient_id) REFERENCES ingredient(id) ON DELETE CASCADE,
    FOREIGN KEY (plat_id) REFERENCES plat(id) ON DELETE CASCADE
);

-- Création de la table CATEGORIE
DROP TABLE IF EXISTS categorie;
CREATE TABLE categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

-- Table d'association PLAT <-> CATEGORIE
DROP TABLE IF EXISTS plat_categorie;
