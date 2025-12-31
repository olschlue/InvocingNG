<?php
/**
 * Benutzer-Klasse für InvoicingNG
 * Verwaltet Authentifizierung und Benutzerverwaltung
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Benutzer authentifizieren
     */
    public function authenticate($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Letzten Login aktualisieren
            $this->updateLastLogin($user['id']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Letzten Login-Zeitstempel aktualisieren
     */
    private function updateLastLogin($userId) {
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
    }
    
    /**
     * Alle Benutzer abrufen
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT id, username, created_at, last_login FROM users ORDER BY username");
        return $stmt->fetchAll();
    }
    
    /**
     * Benutzer nach ID abrufen
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, username, created_at, last_login FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Benutzer nach Benutzername abrufen
     */
    public function getByUsername($username) {
        $stmt = $this->db->prepare("SELECT id, username, created_at, last_login FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    /**
     * Neuen Benutzer erstellen
     */
    public function create($username, $password) {
        // Prüfen, ob Benutzername bereits existiert
        if ($this->getByUsername($username)) {
            return false;
        }
        
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        $result = $stmt->execute([$username, $passwordHash]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Passwort eines Benutzers ändern
     */
    public function changePassword($userId, $newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$passwordHash, $userId]);
    }
    
    /**
     * Benutzer löschen
     */
    public function delete($id) {
        // Admin-Benutzer darf nicht gelöscht werden
        $user = $this->getById($id);
        if ($user && $user['username'] === 'admin') {
            return false;
        }
        
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Benutzername ändern
     */
    public function updateUsername($id, $newUsername) {
        // Prüfen, ob neuer Benutzername bereits existiert
        $existing = $this->getByUsername($newUsername);
        if ($existing && $existing['id'] != $id) {
            return false;
        }
        
        $stmt = $this->db->prepare("UPDATE users SET username = ? WHERE id = ?");
        return $stmt->execute([$newUsername, $id]);
    }
}
