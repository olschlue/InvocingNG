<?php
/**
 * PDF-Generator für InvoicingNG
 * Erstellt PDF-Rechnungen mit FPDF
 */

require_once BASE_PATH . '/vendor/fpdf/fpdf.php';

class InvoicePDF extends FPDF {
    private $invoice;
    private $customer;
    private $items;
    private $company;
    private $euro = 'EUR';
    
    public function setInvoiceData($invoice, $customer, $items, $company) {
        $this->invoice = $invoice;
        $this->customer = $customer;
        $this->items = $items;
        $this->company = $company;
    }
    
    // UTF-8 zu ISO-8859-1 konvertieren (für FPDF)
    private function convertEncoding($text) {
        return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }
    
    // Kopfzeile
    function Header() {
        // Logo (falls vorhanden)
        if (!empty($this->company['logo_path']) && file_exists($this->company['logo_path'])) {
            $this->Image($this->company['logo_path'], 10, 6, 30);
        }
        
        // Firmeninfo rechts
        $this->SetFont('Arial', 'B', 10);
        $this->SetXY(120, 10);
        $this->Cell(80, 5, $this->convertEncoding($this->company['company_name']), 0, 1, 'R');
        
        $this->SetFont('Arial', '', 8);
        $this->SetX(120);
        $this->Cell(80, 4, $this->convertEncoding($this->company['address_street']), 0, 1, 'R');
        $this->SetX(120);
        $this->Cell(80, 4, $this->convertEncoding($this->company['address_zip'] . ' ' . $this->company['address_city']), 0, 1, 'R');
        $this->SetX(120);
        $this->Cell(80, 4, 'Tel: ' . $this->company['phone'], 0, 1, 'R');
        $this->SetX(120);
        $this->Cell(80, 4, 'E-Mail: ' . $this->company['email'], 0, 1, 'R');
        
        $this->Ln(10);
    }
    
    // Fußzeile
    function Footer() {
        $this->SetY(-30);
        $this->SetFont('Arial', '', 7);
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(2);
        
        // Bankverbindung und weitere Infos
        $this->SetX(10);
        $col1Width = 63;
        $col2Width = 63;
        $col3Width = 64;
        
        $this->Cell($col1Width, 4, $this->convertEncoding($this->company['company_name']), 0, 0);
        $this->Cell($col2Width, 4, 'IBAN: ' . $this->company['iban'], 0, 0);
        $this->Cell($col3Width, 4, 'Steuernr.: ' . $this->company['tax_id'], 0, 1);
        
        $this->SetX(10);
        $this->Cell($col1Width, 4, $this->convertEncoding($this->company['address_street']), 0, 0);
        $this->Cell($col2Width, 4, 'BIC: ' . $this->company['bic'], 0, 0);
        $this->Cell($col3Width, 4, 'E-Mail: ' . $this->company['email'], 0, 1);
        
        $this->SetX(10);
        $this->Cell($col1Width, 4, $this->convertEncoding($this->company['address_zip'] . ' ' . $this->company['address_city']), 0, 0);
        $this->Cell($col2Width, 4, '', 0, 0);
        $this->Cell($col3Width, 4, 'Tel: ' . $this->company['phone'], 0, 1);
    }
    
    // Rechnung erstellen
    public function createInvoice() {
        $this->AddPage();
        
        // Kundenadresse
        $this->SetFont('Arial', '', 10);
        $this->SetY(50);
        
        $customerName = !empty($this->customer['company_name']) 
            ? $this->customer['company_name'] 
            : $this->customer['first_name'] . ' ' . $this->customer['last_name'];
        
        $this->Cell(0, 5, $this->convertEncoding($customerName), 0, 1);
        $this->Cell(0, 5, $this->convertEncoding($this->customer['address_street']), 0, 1);
        $this->Cell(0, 5, $this->convertEncoding($this->customer['address_zip'] . ' ' . $this->customer['address_city']), 0, 1);
        
        $this->Ln(10);
        
        // Rechnungsdaten
        $this->SetFont('Arial', '', 9);
        $this->Cell(100, 5, '', 0, 0);
        $this->Cell(40, 5, 'Rechnungsnummer:', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->invoice['invoice_number'], 0, 1);
        
        $this->SetFont('Arial', '', 9);
        $this->Cell(100, 5, '', 0, 0);
        $this->Cell(40, 5, 'Rechnungsdatum:', 0, 0);
        $this->Cell(0, 5, date('d.m.Y', strtotime($this->invoice['invoice_date'])), 0, 1);
        
        $this->Cell(100, 5, '', 0, 0);
        $this->Cell(40, 5, $this->convertEncoding('Fälligkeitsdatum:'), 0, 0);
        $this->Cell(0, 5, date('d.m.Y', strtotime($this->invoice['due_date'])), 0, 1);
        
        if (!empty($this->customer['customer_number'])) {
            $this->Cell(100, 5, '', 0, 0);
            $this->Cell(40, 5, 'Kundennummer:', 0, 0);
            $this->Cell(0, 5, $this->customer['customer_number'], 0, 1);
        }
        
        $this->Ln(10);
        
        // Überschrift
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, 'Rechnung', 0, 1);
        $this->Ln(5);
        
        // Tabellenkopf
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(10, 7, 'Pos', 1, 0, 'C', true);
        $this->Cell(90, 7, 'Beschreibung', 1, 0, 'L', true);
        $this->Cell(20, 7, 'Menge', 1, 0, 'C', true);
        $this->Cell(30, 7, 'Einzelpreis', 1, 0, 'R', true);
        $this->Cell(20, 7, 'MwSt.', 1, 0, 'C', true);
        $this->Cell(30, 7, 'Gesamt', 1, 1, 'R', true);
        
        // Positionen
        $this->SetFont('Arial', '', 9);
        foreach ($this->items as $item) {
            $this->Cell(10, 6, $item['position'], 1, 0, 'C');
            $this->Cell(90, 6, $this->convertEncoding($item['description']), 1, 0, 'L');
            $this->Cell(20, 6, number_format($item['quantity'], 2, ',', '.'), 1, 0, 'C');
            $this->Cell(30, 6, number_format($item['unit_price'], 2, ',', '.') . ' ' . $this->euro, 1, 0, 'R');
            $this->Cell(20, 6, number_format($item['tax_rate'], 0) . '%', 1, 0, 'C');
            $this->Cell(30, 6, number_format($item['total'], 2, ',', '.') . ' ' . $this->euro, 1, 1, 'R');
        }
        
        // Summen
        $this->Ln(2);
        $this->SetFont('Arial', '', 9);
        $this->Cell(140, 6, '', 0, 0);
        $this->Cell(30, 6, 'Nettobetrag:', 0, 0, 'R');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(30, 6, number_format($this->invoice['subtotal'], 2, ',', '.') . ' ' . $this->euro, 0, 1, 'R');
        
        $this->SetFont('Arial', '', 9);
        $this->Cell(140, 6, '', 0, 0);
        $this->Cell(30, 6, 'MwSt. (' . number_format($this->invoice['tax_rate'], 0) . '%):', 0, 0, 'R');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(30, 6, number_format($this->invoice['tax_amount'], 2, ',', '.') . ' ' . $this->euro, 0, 1, 'R');
        
        $this->SetDrawColor(0, 0, 0);
        $this->Line(140, $this->GetY(), 200, $this->GetY());
        $this->Ln(1);
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(140, 7, '', 0, 0);
        $this->Cell(30, 7, 'Gesamtbetrag:', 0, 0, 'R');
        $this->Cell(30, 7, number_format($this->invoice['total_amount'], 2, ',', '.') . ' ' . $this->euro, 0, 1, 'R');
        
        // Zahlungshinweise
        if (!empty($this->invoice['payment_terms'])) {
            $this->Ln(10);
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(0, 5, $this->convertEncoding($this->invoice['payment_terms']));
        }
        
        // Notizen
        if (!empty($this->invoice['notes'])) {
            $this->Ln(5);
            $this->SetFont('Arial', 'I', 9);
            $this->MultiCell(0, 5, $this->convertEncoding('Hinweis: ' . $this->invoice['notes']));
        }
    }
}

class PDFGenerator {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * PDF für Rechnung erstellen
     */
    public function generateInvoicePDF($invoiceId, $output = 'I') {
        // Rechnungsdaten laden
        $invoiceObj = new Invoice();
        $invoice = $invoiceObj->getById($invoiceId);
        $items = $invoiceObj->getItems($invoiceId);
        
        if (!$invoice) {
            die('Rechnung nicht gefunden');
        }
        
        // Kundendaten
        $customer = [
            'company_name' => $invoice['company_name'],
            'first_name' => $invoice['first_name'],
            'last_name' => $invoice['last_name'],
            'address_street' => $invoice['address_street'],
            'address_city' => $invoice['address_city'],
            'address_zip' => $invoice['address_zip'],
            'customer_number' => $invoice['customer_number']
        ];
        
        // Firmendaten
        $stmt = $this->db->query("SELECT * FROM company_settings LIMIT 1");
        $company = $stmt->fetch();
        
        // PDF erstellen
        $pdf = new InvoicePDF();
        $pdf->setInvoiceData($invoice, $customer, $items, $company);
        $pdf->createInvoice();
        
        // Ausgeben oder speichern
        $filename = 'Rechnung_' . $invoice['invoice_number'] . '.pdf';
        $pdf->Output($output, $filename);
    }
}
