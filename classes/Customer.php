<?php
/**
 * Kunden-Klasse für InvoicingNG
 * Verwaltet alle Kunden-Operationen
 */

class Customer {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Alle Kunden abrufen
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM customers ORDER BY customer_number ASC");
        return $stmt->fetchAll();
    }
    
    /**
     * Kunde nach ID abrufen
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Kunde nach Kundennummer abrufen
     */
    public function getByCustomerNumber($customerNumber) {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE customer_number = ?");
        $stmt->execute([$customerNumber]);
        return $stmt->fetch();
    }
    
    /**
     * Neuen Kunden erstellen
     */
    public function create($data) {
        $sql = "INSERT INTO customers (
                    customer_number, company_name, first_name, last_name, 
                    email, phone, address_street, address_city, 
                    address_zip, address_country, vat_id, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['customer_number'],
            $data['company_name'] ?? null,
            $data['first_name'] ?? null,
            $data['last_name'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['address_street'] ?? null,
            $data['address_city'] ?? null,
            $data['address_zip'] ?? null,
            $data['address_country'] ?? 'Deutschland',
            $data['vat_id'] ?? null,
            $data['notes'] ?? null
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Kunde aktualisieren
     */
    public function update($id, $data) {
        $sql = "UPDATE customers SET 
                    customer_number = ?, company_name = ?, first_name = ?, 
                    last_name = ?, email = ?, phone = ?, address_street = ?, 
                    address_city = ?, address_zip = ?, address_country = ?, 
                    vat_id = ?, notes = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['customer_number'],
            $data['company_name'] ?? null,
            $data['first_name'] ?? null,
            $data['last_name'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['address_street'] ?? null,
            $data['address_city'] ?? null,
            $data['address_zip'] ?? null,
            $data['address_country'] ?? 'Deutschland',
            $data['vat_id'] ?? null,
            $data['notes'] ?? null,
            $id
        ]);
    }
    
    /**
     * Kunde löschen
     */
    public function delete($id) {
        // Prüfen, ob Kunde Rechnungen hat
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM invoices WHERE customer_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Kunde kann nicht gelöscht werden, da Rechnungen vorhanden sind.'];
        }
        
        $stmt = $this->db->prepare("DELETE FROM customers WHERE id = ?");
        $result = $stmt->execute([$id]);
        return ['success' => $result, 'message' => $result ? 'Kunde erfolgreich gelöscht.' : 'Fehler beim Löschen.'];
    }
    
    /**
     * Suche nach Kunden
     */
    public function search($term) {
        $searchTerm = "%$term%";
        $sql = "SELECT * FROM customers 
                WHERE company_name LIKE ? 
                   OR first_name LIKE ? 
                   OR last_name LIKE ? 
                   OR email LIKE ? 
                   OR customer_number LIKE ?
                ORDER BY company_name, last_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
    
    /**
     * Nächste Kundennummer generieren
     */
    public function generateCustomerNumber() {
        $stmt = $this->db->query("SELECT customer_number FROM customers ORDER BY id DESC LIMIT 1");
        $lastCustomer = $stmt->fetch();
        
        if ($lastCustomer) {
            $lastNumber = (int) filter_var($lastCustomer['customer_number'], FILTER_SANITIZE_NUMBER_INT);
            return 'K' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }
        
        return 'K00001';
    }
}
