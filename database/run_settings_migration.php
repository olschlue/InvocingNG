<?php
/**
 * Migrations-Skript für Settings-Tabelle
 */

require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starte Migration für Settings-Tabelle...\n";
    
    // SQL-Migration laden und ausführen
    $sql = file_get_contents(__DIR__ . '/migration_add_settings.sql');
    
    // SQL in einzelne Statements aufteilen
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            echo "Führe aus: " . substr($statement, 0, 50) . "...\n";
            $db->exec($statement);
        }
    }
    
    echo "\n✓ Migration erfolgreich abgeschlossen!\n";
    echo "Die Settings-Tabelle wurde erstellt und mit Standard-Einstellungen gefüllt.\n";
    echo "Sie können nun unter 'Meine Firma' die Einstellungen anpassen.\n";
    
} catch (PDOException $e) {
    echo "\n✗ Fehler bei der Migration:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
