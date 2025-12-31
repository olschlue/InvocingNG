
-- Migration: Benutzer-Tabelle erstellen
-- Datum: 2025-12-31
-- Beschreibung: Erstellt die users-Tabelle für Authentifizierung

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username)
);

-- Ersten Admin-Benutzer anlegen
-- Passwort: ee97mnee
INSERT INTO users (username, password_hash) 
VALUES ('admin', '$2y$10$wPQYPMN3WpVoliNeEhKCselzfTbLHD5tX9EbH/THNlG5BLUdiQrs6');