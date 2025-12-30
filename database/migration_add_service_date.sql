-- Migration: Leistungsdatum zur invoices Tabelle hinzufügen
-- Datum: 2025-12-30

USE invoicing_db;

-- Spalte service_date hinzufügen (falls nicht vorhanden)
ALTER TABLE invoices 
ADD COLUMN IF NOT EXISTS service_date DATE AFTER invoice_date;

-- Für bestehende Rechnungen das Leistungsdatum auf das Rechnungsdatum setzen
UPDATE invoices 
SET service_date = invoice_date 
WHERE service_date IS NULL;
