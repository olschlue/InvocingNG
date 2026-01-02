<?php
/**
 * Migrations-Skript für VAT-ID
 */

require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starte Migration für VAT-ID...\n";
    
    // Prüfen ob vat_id Spalte bereits existiert
    $stmt = $db->query("SHOW COLUMNS FROM customers LIKE 'vat_id'");
    if ($stmt->rowCount() == 0) {
        echo "Füge vat_id Spalte zur customers Tabelle hinzu...\n";
        $db->exec("ALTER TABLE customers ADD COLUMN vat_id VARCHAR(50) AFTER tax_id");
        echo "✓ vat_id Spalte hinzugefügt\n";
    } else {
        echo "✓ vat_id Spalte existiert bereits\n";
    }
    
    // VAT-ID zur settings Tabelle hinzufügen
    echo "Füge company_vat_id zu settings hinzu...\n";
    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('company_vat_id', '') ON DUPLICATE KEY UPDATE setting_key = setting_key");
    $stmt->execute();
    echo "✓ company_vat_id zu settings hinzugefügt\n";
    
    echo "\n✓ Migration erfolgreich abgeschlossen!\n";
    echo "Die VAT-ID Felder wurden hinzugefügt.\n";
    
} catch (PDOException $e) {
    echo "\n✗ Fehler bei der Migration:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
