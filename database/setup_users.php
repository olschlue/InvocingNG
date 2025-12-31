<?php
/**
 * Einmaliges Setup-Skript zur Erstellung der Users-Tabelle
 * und des Admin-Benutzers
 */

require_once '../config/config.php';
require_once '../classes/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starte Benutzer-Migration...\n\n";
    
    // Users-Tabelle erstellen
    echo "1. Erstelle users-Tabelle...\n";
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        INDEX idx_username (username)
    )";
    $db->exec($sql);
    echo "   ✓ Tabelle erstellt\n\n";
    
    // Prüfen, ob Admin-Benutzer bereits existiert
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetchColumn() > 0;
    
    if ($adminExists) {
        echo "2. Admin-Benutzer existiert bereits.\n";
        echo "   Soll das Passwort zurückgesetzt werden? (j/n): ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        
        if (strtolower($line) === 'j' || strtolower($line) === 'y') {
            $passwordHash = password_hash('ee97mnee', PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
            $stmt->execute([$passwordHash]);
            echo "   ✓ Admin-Passwort wurde zurückgesetzt\n";
        } else {
            echo "   - Passwort wurde nicht geändert\n";
        }
    } else {
        echo "2. Erstelle Admin-Benutzer...\n";
        $passwordHash = password_hash('ee97mnee', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        $stmt->execute(['admin', $passwordHash]);
        echo "   ✓ Admin-Benutzer erstellt\n";
        echo "   Benutzername: admin\n";
        echo "   Passwort: ee97mnee\n";
    }
    
    echo "\n✓ Migration erfolgreich abgeschlossen!\n";
    echo "\nSie können sich jetzt unter public/login.php anmelden.\n";
    
} catch (PDOException $e) {
    echo "✗ Fehler: " . $e->getMessage() . "\n";
    exit(1);
}
