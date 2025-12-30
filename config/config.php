<?php
/**
 * Konfigurationsdatei für InvoicingNG
 */

// Datenbank-Konfiguration
define('DB_HOST', 'localhost');
define('DB_NAME', 'invoicing_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Applikations-Einstellungen
define('APP_NAME', 'InvoicingNG');
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

// Währung
define('CURRENCY', 'EUR');
define('CURRENCY_SYMBOL', '€');

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
