<?php
/**
 * Stellt sicher, dass company_vat_id in settings existiert
 */

require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Prüfe company_vat_id Einstellung...\n";
    
    // Prüfen ob company_vat_id bereits existiert
    $stmt = $db->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = 'company_vat_id'");
    $stmt->execute();
    $exists = $stmt->fetchColumn() > 0;
    
    if (!$exists) {
        echo "Füge company_vat_id zu settings hinzu...\n";
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('company_vat_id', '')");
        $stmt->execute();
        echo "✓ company_vat_id wurde hinzugefügt\n";
    } else {
        echo "✓ company_vat_id existiert bereits\n";
    }
    
    echo "\nFertig!\n";
    
} catch (PDOException $e) {
    echo "\n✗ Fehler:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
