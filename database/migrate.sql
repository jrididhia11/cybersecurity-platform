-- Migration : à exécuter si vous aviez déjà la BDD installée
-- (ajoute les colonnes et tables manquantes sans tout recréer)

USE cybersecurity_platform;

-- Ajouter la colonne status si elle n'existe pas
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS status ENUM('active','suspended') DEFAULT 'active',
    ADD COLUMN IF NOT EXISTS completed_labs TEXT DEFAULT NULL;

-- Créer la table logs si elle n'existe pas
CREATE TABLE IF NOT EXISTS logs (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT,
    action     VARCHAR(255),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Créer la table failed_logins si elle n'existe pas
CREATE TABLE IF NOT EXISTS failed_logins (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(100),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
