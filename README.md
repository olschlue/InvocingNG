# InvoicingNG - Professionelle Rechnungsverwaltung

Eine vollständige PHP-Applikation zur Verwaltung von Rechnungen, Kunden und Zahlungen mit PDF-Export.

## Features

- ✅ **Kundenverwaltung**: Erstellen, bearbeiten und verwalten Sie Ihre Kunden
- ✅ **Rechnungserstellung**: Professionelle Rechnungen mit mehreren Positionen
- ✅ **PDF-Export**: Automatische Generierung von PDF-Rechnungen
- ✅ **Zahlungsverwaltung**: Erfassen und verwalten Sie Zahlungseingänge
- ✅ **Statusverfolgung**: Verfolgen Sie den Status Ihrer Rechnungen (Entwurf, Versendet, Bezahlt, Überfällig)
- ✅ **Dashboard**: Übersichtliche Statistiken und Kennzahlen
- ✅ **Automatische Berechnungen**: MwSt und Summen werden automatisch berechnet

## Systemanforderungen

- PHP 7.4 oder höher
- MySQL 5.7 oder höher
- Webserver (Apache, Nginx)
- FPDF Bibliothek für PDF-Generierung

## Installation

### 1. Dateien hochladen

Laden Sie alle Dateien in Ihr Webverzeichnis hoch (z.B. `/var/www/html/invoicing`).

### 2. Datenbank einrichten

Importieren Sie das SQL-Schema in Ihre MySQL-Datenbank:

```bash
mysql -u root -p < database/schema.sql
```

Oder führen Sie die Datei `database/schema.sql` über phpMyAdmin aus.

### 3. Konfiguration anpassen

Bearbeiten Sie die Datei `config/config.php` und passen Sie die Datenbankverbindung an:

```php
define('DB_HOST', 'localhost');     // Ihr Datenbankhost
define('DB_NAME', 'invoicing_db');  // Ihr Datenbankname
define('DB_USER', 'root');          // Ihr Datenbankbenutzer
define('DB_PASS', '');              // Ihr Datenbankpasswort
```

### 4. FPDF installieren

FPDF wird für die PDF-Generierung benötigt. Erstellen Sie einen `vendor`-Ordner und laden Sie FPDF herunter:

```bash
mkdir vendor
cd vendor
wget http://www.fpdf.org/en/download/fpdf185.zip
unzip fpdf185.zip
mkdir fpdf
mv fpdf.php fpdf/
```

Oder installieren Sie es mit Composer:

```bash
composer require setasign/fpdf
```

### 5. Verzeichnisse erstellen

Erstellen Sie die benötigten Verzeichnisse mit Schreibrechten:

```bash
mkdir uploads pdfs temp
chmod 755 uploads pdfs temp
```

### 6. Firmeneinstellungen anpassen

Nach der Installation können Sie die Firmeneinstellungen direkt in der Datenbank anpassen:

```sql
UPDATE company_settings 
SET company_name = 'Ihre Firma GmbH',
    address_street = 'Ihre Straße 123',
    address_city = 'Ihre Stadt',
    address_zip = '12345',
    phone = '+49 123 456789',
    email = 'info@ihre-firma.de',
    tax_id = 'DE123456789',
    iban = 'DE89370400440532013000',
    bic = 'COBADEFFXXX'
WHERE id = 1;
```

### 7. PDF-Hintergrundbild (Optional)

Um ein Hintergrundbild für Ihre PDF-Rechnungen zu verwenden:

1. Erstellen Sie ein PNG-Bild in A4-Größe (empfohlen: 2480 x 3508 Pixel bei 300 DPI)
2. Speichern Sie es als `public/assets/invoice_background.png`
3. Das Bild wird automatisch auf allen PDF-Seiten als Hintergrund eingefügt

Details siehe `public/assets/README.md`
    bic = 'COBADEFFXXX'
WHERE id = 1;
```

## Verwendung

### Applikation öffnen

Öffnen Sie die Applikation in Ihrem Browser:

```
http://localhost/invoicing/public/
```

### Erste Schritte

1. **Kunden anlegen**: Navigieren Sie zu "Kunden" und legen Sie Ihre ersten Kunden an
2. **Rechnung erstellen**: Gehen Sie zu "Rechnungen" → "Neue Rechnung"
3. **Positionen hinzufügen**: Fügen Sie Rechnungspositionen zur Rechnung hinzu
4. **PDF generieren**: Klicken Sie auf "PDF" um die Rechnung als PDF anzuzeigen
5. **Zahlung erfassen**: Erfassen Sie Zahlungseingänge unter "Zahlungen"

## Projektstruktur

```
InvoicingNG/
├── classes/              # PHP-Klassen
│   ├── Database.php      # Datenbankverbindung
│   ├── Customer.php      # Kundenverwaltung
│   ├── Invoice.php       # Rechnungsverwaltung
│   ├── Payment.php       # Zahlungsverwaltung
│   └── PDFGenerator.php  # PDF-Generierung
├── config/               # Konfigurationsdateien
│   └── config.php        # Hauptkonfiguration
├── database/             # Datenbankschema
│   └── schema.sql        # SQL-Schema
├── public/               # Öffentliches Verzeichnis
│   ├── includes/         # Header & Footer
│   ├── pages/            # Seiten
│   └── index.php         # Hauptdatei
├── uploads/              # Upload-Verzeichnis
├── pdfs/                 # PDF-Verzeichnis
└── temp/                 # Temp-Verzeichnis
```

## Datenbank-Schema

### Tabellen

- **customers**: Kundendaten
- **invoices**: Rechnungskopfdaten
- **invoice_items**: Rechnungspositionen
- **payments**: Zahlungseingänge
- **company_settings**: Firmeneinstellungen

### Wichtige Features

- Automatische Trigger zur Berechnung von Rechnungssummen
- Foreign Keys zur Datenintegrität
- Indizes für optimale Performance

## Sicherheitshinweise

⚠️ **Wichtig für Produktivumgebung:**

1. **Passwörter schützen**: Speichern Sie Datenbankzugangsdaten außerhalb des Webverzeichnisses
2. **Fehleranzeige deaktivieren**: Setzen Sie `display_errors` auf `0` in der Produktion
3. **HTTPS verwenden**: Verwenden Sie SSL/TLS für verschlüsselte Verbindungen
4. **Zugriffsrechte**: Schützen Sie das `config/` Verzeichnis vor direktem Zugriff
5. **SQL-Injection**: Die Applikation verwendet Prepared Statements (PDO)
6. **Backups**: Erstellen Sie regelmäßige Datenbank-Backups

## Anpassungen

### Design anpassen

Das Design kann in `public/includes/header.php` im `<style>`-Bereich angepasst werden.

### PDF-Layout ändern

Die PDF-Generierung kann in `classes/PDFGenerator.php` angepasst werden.

### Weitere Felder hinzufügen

1. Fügen Sie das Feld zur Datenbank hinzu
2. Passen Sie die entsprechende PHP-Klasse an
3. Aktualisieren Sie die Formulare in den `pages/` Dateien

## Support & Lizenz

Diese Applikation wurde als vollständiges Rechnungsverwaltungssystem entwickelt.

**Version:** 1.0.0  
**Datum:** Dezember 2025

## Wartung

### Datenbank-Backup erstellen

```bash
mysqldump -u root -p invoicing_db > backup_$(date +%Y%m%d).sql
```

### Updates einspielen

Sichern Sie vor Updates immer Ihre Datenbank und Dateien!

## Fehlerbehebung

### "Datenbankverbindung fehlgeschlagen"
- Überprüfen Sie die Zugangsdaten in `config/config.php`
- Stellen Sie sicher, dass der MySQL-Server läuft

### "PDF kann nicht erstellt werden"
- Überprüfen Sie, ob FPDF korrekt installiert ist
- Prüfen Sie die Schreibrechte für das `pdfs/` Verzeichnis

### "Seite wird nicht gefunden"
- Überprüfen Sie die `.htaccess` Konfiguration
- Stellen Sie sicher, dass mod_rewrite aktiviert ist

## Technische Details

- **Framework**: Vanilla PHP (kein Framework erforderlich)
- **Datenbank**: MySQL mit InnoDB Engine
- **PDF-Library**: FPDF
- **Architektur**: MVC-ähnlich mit separaten Klassen
- **Security**: PDO Prepared Statements, Input Validation