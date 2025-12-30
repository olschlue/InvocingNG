<?php
/**
 * Datenbank-Klasse für InvoicingNG
 * Verwaltet die Datenbankverbindung mit PDO
 */

class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Private Konstruktor für Singleton-Pattern
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
        }
    }
    
    /**
     * Singleton-Instanz abrufen
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * PDO-Verbindung zurückgeben
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Klonen verhindern
     */
    private function __clone() {}
    
    /**
     * Deserialisierung verhindern
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
