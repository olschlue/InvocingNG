-- Migration: VAT-ID für Kunden und Firma hinzufügen

-- VAT-ID Spalte zur customers Tabelle hinzufügen (wenn nicht vorhanden)
ALTER TABLE customers ADD COLUMN IF NOT EXISTS vat_id VARCHAR(50) AFTER tax_id;

-- VAT-ID zur settings Tabelle hinzufügen (für Firmeneinstellungen)
INSERT INTO settings (setting_key, setting_value) VALUES ('company_vat_id', '')
ON DUPLICATE KEY UPDATE setting_key = setting_key;
