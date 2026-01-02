-- InvoicingNG - Datenbank Schema
-- MySQL Datenbank für Rechnungsverwaltung

-- Datenbank erstellen
CREATE DATABASE IF NOT EXISTS invoicing_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE invoicing_db;

-- Tabelle für Kunden
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_number VARCHAR(50) UNIQUE NOT NULL,
    company_name VARCHAR(255),
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(50),
    address_street VARCHAR(255),
    address_city VARCHAR(100),
    address_zip VARCHAR(20),
    address_country VARCHAR(100) DEFAULT 'Deutschland',
    tax_id VARCHAR(50),
    vat_id VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_customer_number (customer_number),
    INDEX idx_company_name (company_name),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabelle für Rechnungen
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    invoice_date DATE NOT NULL,
    service_date DATE,
    due_date DATE NOT NULL,
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    subtotal DECIMAL(10, 2) DEFAULT 0.00,
    tax_rate DECIMAL(5, 2) DEFAULT 19.00,
    tax_amount DECIMAL(10, 2) DEFAULT 0.00,
    total_amount DECIMAL(10, 2) DEFAULT 0.00,
    notes TEXT,
    payment_terms TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_customer_id (customer_id),
    INDEX idx_invoice_date (invoice_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabelle für Rechnungspositionen
CREATE TABLE IF NOT EXISTS invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    position INT NOT NULL,
    description TEXT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL DEFAULT 1.00,
    unit_price DECIMAL(10, 2) NOT NULL,
    tax_rate DECIMAL(5, 2) DEFAULT 19.00,
    total DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice_id (invoice_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabelle für Zahlungen
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    payment_date DATE NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'credit_card', 'paypal', 'other') DEFAULT 'bank_transfer',
    reference VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE RESTRICT,
    INDEX idx_invoice_id (invoice_id),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabelle für Firmeneinstellungen
CREATE TABLE IF NOT EXISTS company_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    address_street VARCHAR(255),
    address_city VARCHAR(100),
    address_zip VARCHAR(20),
    address_country VARCHAR(100) DEFAULT 'Deutschland',
    phone VARCHAR(50),
    email VARCHAR(255),
    website VARCHAR(255),
    tax_id VARCHAR(50),
    vat_id VARCHAR(50),
    bank_name VARCHAR(255),
    bank_account VARCHAR(100),
    bank_code VARCHAR(50),
    iban VARCHAR(50),
    bic VARCHAR(50),
    logo_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Beispieldaten einfügen
INSERT INTO company_settings (company_name, address_street, address_city, address_zip, phone, email, tax_id, iban, bic)
VALUES ('Ihre Firma GmbH', 'Musterstraße 123', 'Berlin', '10115', '+49 30 12345678', 'info@ihre-firma.de', 'DE123456789', 'DE89370400440532013000', 'COBADEFFXXX');

-- Tabelle für Firmen- und Systemeinstellungen
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Standard-Einstellungen einfügen
INSERT INTO settings (setting_key, setting_value) VALUES
('company_name', 'Schlüter & Friends'),
('app_name', 'Rechnungen'),
('smtp_host', 'smtp.ionos.de'),
('smtp_port', '465'),
('smtp_user', 'noreply@oschlueter.de'),
('smtp_pass', 'EE97mnee##'),
('smtp_from', 'noreply@oschlueter.de'),
('smtp_from_name', 'Schlüter & Friends'),
('smtp_encryption', 'ssl')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Tabelle für Benutzer
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




-- Trigger zum automatischen Aktualisieren der Rechnungssummen
DELIMITER //

CREATE TRIGGER update_invoice_totals AFTER INSERT ON invoice_items
FOR EACH ROW
BEGIN
    UPDATE invoices 
    SET subtotal = (
        SELECT SUM(total) FROM invoice_items WHERE invoice_id = NEW.invoice_id
    ),
    tax_amount = (
        SELECT SUM(total * tax_rate / 100) FROM invoice_items WHERE invoice_id = NEW.invoice_id
    ),
    total_amount = (
        SELECT SUM(total + (total * tax_rate / 100)) FROM invoice_items WHERE invoice_id = NEW.invoice_id
    )
    WHERE id = NEW.invoice_id;
END//

CREATE TRIGGER update_invoice_totals_on_update AFTER UPDATE ON invoice_items
FOR EACH ROW
BEGIN
    UPDATE invoices 
    SET subtotal = (
        SELECT SUM(total) FROM invoice_items WHERE invoice_id = NEW.invoice_id
    ),
    tax_amount = (
        SELECT SUM(total * tax_rate / 100) FROM invoice_items WHERE invoice_id = NEW.invoice_id
    ),
    total_amount = (
        SELECT SUM(total + (total * tax_rate / 100)) FROM invoice_items WHERE invoice_id = NEW.invoice_id
    )
    WHERE id = NEW.invoice_id;
END//

CREATE TRIGGER update_invoice_totals_on_delete AFTER DELETE ON invoice_items
FOR EACH ROW
BEGIN
    UPDATE invoices 
    SET subtotal = COALESCE((
        SELECT SUM(total) FROM invoice_items WHERE invoice_id = OLD.invoice_id
    ), 0),
    tax_amount = COALESCE((
        SELECT SUM(total * tax_rate / 100) FROM invoice_items WHERE invoice_id = OLD.invoice_id
    ), 0),
    total_amount = COALESCE((
        SELECT SUM(total + (total * tax_rate / 100)) FROM invoice_items WHERE invoice_id = OLD.invoice_id
    ), 0)
    WHERE id = OLD.invoice_id;
END//

DELIMITER ;
