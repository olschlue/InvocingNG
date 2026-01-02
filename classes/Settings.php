<?php
/**
 * Settings-Klasse für InvoicingNG
 * Verwaltet Firmen- und Systemeinstellungen
 */

class Settings {
    private $db;
    private static $cache = [];
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Einstellung abrufen
     * 
     * @param string $key Einstellungs-Schlüssel
     * @param mixed $default Standardwert falls nicht gefunden
     * @return mixed Einstellungswert
     */
    public function get($key, $default = null) {
        // Prüfen ob im Cache
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        
        try {
            $stmt = $this->db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            $result = $stmt->fetch();
            
            if ($result) {
                self::$cache[$key] = $result['setting_value'];
                return $result['setting_value'];
            }
        } catch (PDOException $e) {
            error_log("Settings::get error: " . $e->getMessage());
        }
        
        return $default;
    }
    
    /**
     * Einstellung setzen
     * 
     * @param string $key Einstellungs-Schlüssel
     * @param mixed $value Wert
     * @return bool Erfolg
     */
    public function set($key, $value) {
        $stmt = $this->db->prepare("
            INSERT INTO settings (setting_key, setting_value) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE setting_value = ?
        ");
        
        $result = $stmt->execute([$key, $value, $value]);
        
        // Cache aktualisieren
        if ($result) {
            self::$cache[$key] = $value;
        }
        
        return $result;
    }
    
    /**
     * Mehrere Einstellungen auf einmal setzen
     * 
     * @param array $settings Array von Key-Value-Paaren
     * @return bool Erfolg
     */
    public function setMultiple($settings) {
        $success = true;
        foreach ($settings as $key => $value) {
            if (!$this->set($key, $value)) {
                $success = false;
            }
        }
        return $success;
    }
    
    /**
     * Alle Einstellungen abrufen
     * 
     * @return array Alle Einstellungen als Key-Value-Array
     */
    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT setting_key, setting_value FROM settings");
            $settings = [];
            
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            
            return $settings;
        } catch (PDOException $e) {
            error_log("Settings::getAll error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cache leeren
     */
    public static function clearCache() {
        self::$cache = [];
    }
}
