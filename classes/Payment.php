<?php
/**
 * Zahlungs-Klasse für InvoicingNG
 * Verwaltet alle Zahlungs-Operationen
 */

class Payment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Alle Zahlungen abrufen
     */
    public function getAll() {
        $stmt = $this->db->query("
            SELECT p.*, i.invoice_number, c.company_name, c.first_name, c.last_name
            FROM payments p
            LEFT JOIN invoices i ON p.invoice_id = i.id
            LEFT JOIN customers c ON i.customer_id = c.id
            ORDER BY p.payment_date DESC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Zahlung nach ID abrufen
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, i.invoice_number 
            FROM payments p
            LEFT JOIN invoices i ON p.invoice_id = i.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Zahlungen für eine Rechnung abrufen
     */
    public function getByInvoice($invoiceId) {
        $stmt = $this->db->prepare("
            SELECT * FROM payments 
            WHERE invoice_id = ? 
            ORDER BY payment_date DESC
        ");
        $stmt->execute([$invoiceId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Gesamtsumme der Zahlungen für eine Rechnung
     */
    public function getTotalPaidForInvoice($invoiceId) {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0) as total_paid 
            FROM payments 
            WHERE invoice_id = ?
        ");
        $stmt->execute([$invoiceId]);
        $result = $stmt->fetch();
        return $result['total_paid'];
    }
    
    /**
     * Neue Zahlung erstellen
     */
    public function create($data) {
        $sql = "INSERT INTO payments (
                    invoice_id, payment_date, amount, payment_method, 
                    reference, notes
                ) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['invoice_id'],
            $data['payment_date'],
            $data['amount'],
            $data['payment_method'] ?? 'bank_transfer',
            $data['reference'] ?? null,
            $data['notes'] ?? null
        ]);
        
        if ($result) {
            // Rechnungsstatus aktualisieren
            $this->updateInvoiceStatus($data['invoice_id']);
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Zahlung aktualisieren
     */
    public function update($id, $data) {
        $sql = "UPDATE payments SET 
                    invoice_id = ?, payment_date = ?, amount = ?, 
                    payment_method = ?, reference = ?, notes = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['invoice_id'],
            $data['payment_date'],
            $data['amount'],
            $data['payment_method'] ?? 'bank_transfer',
            $data['reference'] ?? null,
            $data['notes'] ?? null,
            $id
        ]);
        
        if ($result) {
            // Rechnungsstatus aktualisieren
            $this->updateInvoiceStatus($data['invoice_id']);
        }
        
        return $result;
    }
    
    /**
     * Zahlung löschen
     */
    public function delete($id) {
        // Invoice ID vor dem Löschen speichern
        $payment = $this->getById($id);
        $invoiceId = $payment['invoice_id'];
        
        $stmt = $this->db->prepare("DELETE FROM payments WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            // Rechnungsstatus aktualisieren
            $this->updateInvoiceStatus($invoiceId);
            return ['success' => true, 'message' => 'Zahlung erfolgreich gelöscht.'];
        }
        
        return ['success' => false, 'message' => 'Fehler beim Löschen.'];
    }
    
    /**
     * Rechnungsstatus basierend auf Zahlungen aktualisieren
     */
    private function updateInvoiceStatus($invoiceId) {
        // Rechnungssumme abrufen
        $stmt = $this->db->prepare("SELECT total_amount FROM invoices WHERE id = ?");
        $stmt->execute([$invoiceId]);
        $invoice = $stmt->fetch();
        
        if (!$invoice) {
            return false;
        }
        
        $totalAmount = $invoice['total_amount'];
        $totalPaid = $this->getTotalPaidForInvoice($invoiceId);
        
        $newStatus = 'sent';
        if ($totalPaid >= $totalAmount) {
            $newStatus = 'paid';
        } elseif ($totalPaid > 0) {
            $newStatus = 'sent'; // Teilweise bezahlt
        }
        
        $stmt = $this->db->prepare("UPDATE invoices SET status = ? WHERE id = ?");
        return $stmt->execute([$newStatus, $invoiceId]);
    }
    
    /**
     * Zahlungsübersicht (Statistiken)
     */
    public function getStatistics($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_payments,
                SUM(amount) as total_amount,
                AVG(amount) as avg_amount,
                MIN(amount) as min_amount,
                MAX(amount) as max_amount
            FROM payments
            WHERE YEAR(payment_date) = ?
        ");
        $stmt->execute([$year]);
        return $stmt->fetch();
    }
    
    /**
     * Zahlungen nach Monat gruppiert
     */
    public function getByMonth($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                MONTH(payment_date) as month,
                COUNT(*) as count,
                SUM(amount) as total
            FROM payments
            WHERE YEAR(payment_date) = ?
            GROUP BY MONTH(payment_date)
            ORDER BY MONTH(payment_date)
        ");
        $stmt->execute([$year]);
        return $stmt->fetchAll();
    }
}
