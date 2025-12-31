<?php
/**
 * Datenbank-Migration: Benutzer-Tabelle erstellen
 * und ersten Admin-Benutzer anlegen
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== Datenbank-Migration: Benutzer-System ===\n\n";
    
    // Prüfen, ob Tabelle bereits existiert
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "⚠️  Tabelle 'users' existiert bereits.\n";
        echo "Migration übersprungen.\n";
        exit(0);
    }
    
    // Tabelle erstellen
    echo "Erstelle Tabelle 'users'...\n";
    $db->exec("
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL,
            INDEX idx_username (username)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Tabelle erstellt\n\n";
    
    // Admin-Benutzer anlegen
    echo "Erstelle Admin-Benutzer...\n";
    $username = 'admin';
    $password = 'ee97mnee';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->execute([$username, $passwordHash]);
    
    echo "✓ Admin-Benutzer erstellt\n";
    echo "  Benutzername: admin\n";
    echo "  Passwort: ee97mnee\n\n";
    
    echo "=== Migration erfolgreich abgeschlossen ===\n";
    echo "\nSie können sich jetzt mit den Admin-Zugangsdaten anmelden.\n";
    
} catch (PDOException $e) {
    echo "❌ Fehler bei der Migration: " . $e->getMessage() . "\n";
    exit(1);
}
