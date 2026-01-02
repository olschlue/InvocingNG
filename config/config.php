<?php
/**
 * Konfigurationsdatei für InvoicingNG
 */

// Datenbank-Konfiguration
define('DB_HOST', 'db5019296438.hosting-data.io');
define('DB_NAME', 'dbs15122806');
define('DB_USER', 'dbu3932847');
define('DB_PASS', 'EE97mnee##ee');
define('DB_CHARSET', 'utf8mb4');

// Applikations-Einstellungen
define('APP_NAME', 'Rechnungen');
define('APP_VERSION', '1.0.0');
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', 'http://localhost');

// Verzeichnisse
define('UPLOAD_DIR', BASE_PATH . '/uploads');
define('PDF_DIR', BASE_PATH . '/pdfs');
define('TEMP_DIR', BASE_PATH . '/temp');

// PDF-Einstellungen
define('PDF_FONT', 'DejaVu Sans');
define('PDF_FONT_SIZE', 10);
define('PDF_BACKGROUND', BASE_PATH . '/public/assets/invoice_background.png');
define('PDF_FILENAME_SUFFIX', 'SF-RE-');  // Suffix für PDF-Dateinamen

// Design & Branding
define('APP_LOGO', BASE_PATH . '/public/assets/logo.png');
define('APP_PRIMARY_COLOR', '#ffa011ff');      // Hauptfarbe (dunkelblau)
define('APP_SECONDARY_COLOR', '#ffa0119f');    // Sekundärfarbe (grau-blau)
define('APP_ACCENT_COLOR', '#e2e2e2ff');       // Akzentfarbe (hellblau)
define('APP_SUCCESS_COLOR', '#27ae60');      // Erfolgsfarbe (grün)
define('APP_WARNING_COLOR', '#f39c12');      // Warnfarbe (orange)
define('APP_DANGER_COLOR', '#e74c3c');       // Fehlerfarbe (rot)
define('APP_INFO_COLOR', '#8f5e87ff');         // Infofarbe (cyan)
define('APP_BACKGROUND_COLOR', '#f1f1f1ff');   // Hintergrundfarbe
define('APP_TEXT_COLOR', '#333333');         // Textfarbe

// Währung
define('CURRENCY', 'EUR');
define('APP_CURRENCY_SYMBOL', '€');

// Rechnungsnummer
define('INVOICE_NUMBER_PREFIX', 'RE-');

// PDF-Anzeige Optionen
define('PDF_SHOW_SERVICE_DATE', true);  // Leistungsdatum auf PDF anzeigen
define('PDF_SHOW_DUE_DATE', true);      // Fälligkeitsdatum auf PDF anzeigen

// E-Mail-Konfiguration
define('SMTP_HOST', 'smtp.ionos.de');        // SMTP-Server
define('SMTP_PORT', 465);                       // SMTP-Port (587 für TLS, 465 für SSL)
define('SMTP_USER', 'noreply@oschlueter.de');  // SMTP-Benutzername
define('SMTP_PASS', 'EE97mnee##');           // SMTP-Passwort
define('SMTP_FROM', 'noreply@oschlueter.de');  // Absender-E-Mail
define('SMTP_FROM_NAME', 'Schlüter & Friends');         // Absender-Name
define('SMTP_ENCRYPTION', 'ssl');               // Verschlüsselung: 'tls' oder 'ssl'

// Sprache
define('APP_LANGUAGE', 'de'); // 'de' oder 'en'

// Datumsformat
define('DATE_FORMAT', 'd.m.Y');

// Fehlerbehandlung
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader
spl_autoload_register(function ($class) {
    $file = BASE_PATH . '/classes/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Sprachdatei laden
$langFile = BASE_PATH . '/lang/' . APP_LANGUAGE . '.php';
if (file_exists($langFile)) {
    require_once $langFile;
} else {
    // Fallback auf Deutsch
    require_once BASE_PATH . '/lang/de.php';
}

/**
 * Hilfsfunktion für Übersetzungen
 * @param string $key Der Übersetzungsschlüssel
 * @return string Die übersetzte Zeichenkette
 */
function __($key) {
    global $lang;
    return $lang[$key] ?? $key;
}
