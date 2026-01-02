<?php
/**
 * Rechnungs-Klasse für InvoicingNG
 * Verwaltet alle Rechnungs-Operationen
 */

class Invoice {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Alle Rechnungen abrufen
     */
    public function getAll($status = null) {
        $sql = "SELECT i.*, c.company_name, c.first_name, c.last_name 
                FROM invoices i
                LEFT JOIN customers c ON i.customer_id = c.id";
        
        if ($status) {
            $sql .= " WHERE i.status = ?";
            $stmt = $this->db->prepare($sql . " ORDER BY i.id DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = $this->db->query($sql . " ORDER BY i.id DESC");
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Rechnung nach ID abrufen
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT i.*, c.* 
            FROM invoices i
            LEFT JOIN customers c ON i.customer_id = c.id
            WHERE i.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Rechnungspositionen abrufen
     */
    public function getItems($invoiceId) {
        $stmt = $this->db->prepare("
            SELECT * FROM invoice_items 
            WHERE invoice_id = ? 
            ORDER BY position
        ");
        $stmt->execute([$invoiceId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Neue Rechnung erstellen
     */
    public function create($data) {
        $sql = "INSERT INTO invoices (
                    invoice_number, customer_id, invoice_date, service_date, due_date, 
                    status, tax_rate, notes, payment_terms
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['invoice_number'],
            $data['customer_id'],
            $data['invoice_date'],
            $data['service_date'] ?? null,
            $data['due_date'],
            $data['status'] ?? 'draft',
            $data['tax_rate'] ?? 19.00,
            $data['notes'] ?? null,
            $data['payment_terms'] ?? null
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Rechnung aktualisieren
     */
    public function update($id, $data) {
        $sql = "UPDATE invoices SET 
                    invoice_number = ?, customer_id = ?, invoice_date = ?, 
                    service_date = ?, due_date = ?, status = ?, tax_rate = ?, notes = ?, 
                    payment_terms = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['invoice_number'],
            $data['customer_id'],
            $data['invoice_date'],
            $data['service_date'] ?? null,
            $data['due_date'],
            $data['status'] ?? 'draft',
            $data['tax_rate'] ?? 19.00,
            $data['notes'] ?? null,
            $data['payment_terms'] ?? null,
            $id
        ]);
    }
    
    /**
     * Rechnung löschen
     */
    public function delete($id) {
        // Prüfen ob Zahlungen vorhanden sind
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM payments WHERE invoice_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Rechnung kann nicht gelöscht werden, da Zahlungen vorhanden sind.'];
        }
        
        // Rechnungspositionen werden durch CASCADE automatisch gelöscht
        $stmt = $this->db->prepare("DELETE FROM invoices WHERE id = ?");
        $result = $stmt->execute([$id]);
        return ['success' => $result, 'message' => $result ? 'Rechnung erfolgreich gelöscht.' : 'Fehler beim Löschen.'];
    }
    
    /**
     * Rechnungsposition hinzufügen
     */
    public function addItem($invoiceId, $data) {
        // Position ermitteln
        $stmt = $this->db->prepare("SELECT MAX(position) as max_pos FROM invoice_items WHERE invoice_id = ?");
        $stmt->execute([$invoiceId]);
        $result = $stmt->fetch();
        $position = ($result['max_pos'] ?? 0) + 1;
        
        // Total berechnen
        $total = $data['quantity'] * $data['unit_price'];
        
        $sql = "INSERT INTO invoice_items (
                    invoice_id, position, description, quantity, 
                    unit_price, tax_rate, total
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $invoiceId,
            $position,
            $data['description'],
            $data['quantity'],
            $data['unit_price'],
            $data['tax_rate'] ?? 19.00,
            $total
        ]);
    }
    
    /**
     * Rechnungsposition aktualisieren
     */
    public function updateItem($itemId, $data) {
        $total = $data['quantity'] * $data['unit_price'];
        
        $sql = "UPDATE invoice_items SET 
                    description = ?, quantity = ?, unit_price = ?, 
                    tax_rate = ?, total = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['description'],
            $data['quantity'],
            $data['unit_price'],
            $data['tax_rate'] ?? 19.00,
            $total,
            $itemId
        ]);
    }
    
    /**
     * Rechnungsposition löschen
     */
    public function deleteItem($itemId) {
        $stmt = $this->db->prepare("DELETE FROM invoice_items WHERE id = ?");
        return $stmt->execute([$itemId]);
    }
    
    /**
     * Nächste Rechnungsnummer generieren
     */
    public function generateInvoiceNumber() {
        $year = date('Y');
        $stmt = $this->db->prepare("
            SELECT invoice_number 
            FROM invoices 
            WHERE invoice_number LIKE ? 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute([$year . '%']);
        $lastInvoice = $stmt->fetch();
        
        if ($lastInvoice) {
            // Extrahiere die Nummer am Ende (ohne führende Nullen)
            preg_match('/(\d+)$/', $lastInvoice['invoice_number'], $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
            return $year . '-' . ($lastNumber + 1);
        }
        
        return $year . '-1';
    }
    
    /**
     * Rechnungsstatus aktualisieren
     */
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE invoices SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
    
    /**
     * Summen für eine Rechnung neu berechnen
     */
    public function calculateTotals($invoiceId) {
        // Alle Positionen abrufen
        $items = $this->getItems($invoiceId);
        
        $subtotal = 0;
        $taxAmount = 0;
        
        // Rechnung abrufen für Steuersatz
        $invoice = $this->getById($invoiceId);
        $taxRate = $invoice['tax_rate'];
        
        // Summen berechnen
        foreach ($items as $item) {
            $subtotal += $item['total'];
        }
        
        $taxAmount = $subtotal * ($taxRate / 100);
        $totalAmount = $subtotal + $taxAmount;
        
        // In Datenbank aktualisieren
        $stmt = $this->db->prepare("
            UPDATE invoices 
            SET subtotal = ?, tax_amount = ?, total_amount = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([$subtotal, $taxAmount, $totalAmount, $invoiceId]);
    }
    
    /**
     * Überfällige Rechnungen abrufen
     */
    public function getOverdue() {
        $stmt = $this->db->query("
            SELECT i.*, c.company_name, c.first_name, c.last_name 
            FROM invoices i
            LEFT JOIN customers c ON i.customer_id = c.id
            WHERE i.due_date < CURDATE() 
            AND i.status NOT IN ('paid', 'cancelled')
            ORDER BY i.due_date ASC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Rechnung kopieren (duplizieren)
     */
    public function duplicate($id) {
        // Original-Rechnung abrufen
        $original = $this->getById($id);
        if (!$original) {
            return false;
        }
        
        // Original-Positionen abrufen
        $originalItems = $this->getItems($id);
        
        // Neue Rechnungsnummer generieren
        $newInvoiceNumber = $this->generateInvoiceNumber();
        
        // Neue Rechnung erstellen
        $newData = [
            'invoice_number' => $newInvoiceNumber,
            'customer_id' => $original['customer_id'],
            'invoice_date' => date('Y-m-d'),
            'service_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+14 days')),
            'status' => 'draft',
            'tax_rate' => $original['tax_rate'],
            'notes' => $original['notes'],
            'payment_terms' => $original['payment_terms']
        ];
        
        $newInvoiceId = $this->create($newData);
        
        if ($newInvoiceId) {
            // Positionen kopieren
            foreach ($originalItems as $item) {
                $itemData = [
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate']
                ];
                $this->addItem($newInvoiceId, $itemData);
            }
            
            // Summen neu berechnen
            $this->calculateTotals($newInvoiceId);
            
            return $newInvoiceId;
        }
        
        return false;
    }
    
    /**
     * Notiz an Rechnung anhängen
     * 
     * @param int $id Rechnungs-ID
     * @param string $additionalNote Zusätzliche Notiz zum Anhängen
     * @return bool Erfolg
     */
    public function appendNote($id, $additionalNote) {
        // Aktuelle Rechnung abrufen
        $invoice = $this->getById($id);
        if (!$invoice) {
            error_log("appendNote: Invoice $id not found");
            return false;
        }
        
        // Neue Notiz zusammenstellen
        $currentNotes = $invoice['notes'] ?? '';
        $newNotes = $currentNotes;
        
        error_log("appendNote: Current notes for invoice $id: " . $currentNotes);
        
        // Trennzeichen hinzufügen, wenn bereits Notizen vorhanden sind
        if (!empty($currentNotes)) {
            $newNotes .= "\n\n";
        }
        
        $newNotes .= $additionalNote;
        
        error_log("appendNote: New notes for invoice $id: " . $newNotes);
        
        // Notiz aktualisieren
        $stmt = $this->db->prepare("UPDATE invoices SET notes = ? WHERE id = ?");
        $result = $stmt->execute([$newNotes, $id]);
        error_log("appendNote: Update result for invoice $id: " . ($result ? 'success' : 'failed'));
        return $result;
    }
}
