-- Tabelle für Firmen- und Systemeinstellungen
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
