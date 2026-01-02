-- Migration: VAT-ID für Kunden und Firma hinzufügen

-- VAT-ID Spalte zur customers Tabelle hinzufügen (wenn nicht vorhanden)
ALTER TABLE customers ADD COLUMN IF NOT EXISTS vat_id VARCHAR(50);

-- VAT-ID Spalte zur company_settings Tabelle hinzufügen (wenn nicht vorhanden)
ALTER TABLE company_settings ADD COLUMN IF NOT EXISTS vat_id VARCHAR(50);
