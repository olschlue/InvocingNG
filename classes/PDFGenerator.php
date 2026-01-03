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
    
    // Berechnet die Anzahl der Zeilen für einen Text in einer MultiCell
    private function NbLines($width, $text) {
        $cw = &$this->CurrentFont['cw'];
        if ($width == 0) {
            $width = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($width - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $text);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
    
    // Kopfzeile
    function Header() {
        // Hintergrundbild vollflächig einfügen (falls vorhanden)
        if (defined('PDF_BACKGROUND') && file_exists(PDF_BACKGROUND)) {
            // A4 Format: 210mm x 297mm
            $this->Image(PDF_BACKGROUND, 0, 0, 210, 297);
        }
        
        $this->Ln(10);
    }
    
    // Fußzeile
    function Footer() {
        // Kein Footer
    }
    
    // Rechnung erstellen
    public function createInvoice() {
        // Ränder auf 20mm links und rechts setzen
        $this->SetLeftMargin(20);
        $this->SetRightMargin(20);
        
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
        
        // VAT-ID anzeigen falls vorhanden
        if (!empty($this->customer['vat_id'])) {
            $this->Cell(0, 5, $this->convertEncoding(__('vat_id') . ': ' . $this->customer['vat_id']), 0, 1);
        }
        
        $this->Ln(10);
        
        // Rechnungsdaten
        $this->SetFont('Arial', '', 9);
        $this->Cell(100, 5, '', 0, 0);
        $this->Cell(49, 5, $this->convertEncoding(__('pdf_invoice_number')) . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $invoiceNumberDisplay = (defined('INVOICE_NUMBER_PREFIX') ? INVOICE_NUMBER_PREFIX : '') . $this->invoice['invoice_number'];
        $this->Cell(0, 5, $invoiceNumberDisplay, 0, 1);
        
        $this->SetFont('Arial', '', 9);
        $this->Cell(100, 5, '', 0, 0);
        $this->Cell(49, 5, $this->convertEncoding(__('pdf_invoice_date')) . ':', 0, 0);
        $this->Cell(0, 5, date('d.m.Y', strtotime($this->invoice['invoice_date'])), 0, 1);
        
        // Leistungsdatum anzeigen (falls konfiguriert)
        if (defined('PDF_SHOW_SERVICE_DATE') && PDF_SHOW_SERVICE_DATE && !empty($this->invoice['service_date'])) {
            $this->Cell(100, 5, '', 0, 0);
            $this->Cell(49, 5, $this->convertEncoding(__('pdf_service_date')) . ':', 0, 0);
            $this->Cell(0, 5, date('d.m.Y', strtotime($this->invoice['service_date'])), 0, 1);
        }
        
        // Fälligkeitsdatum anzeigen (falls konfiguriert)
        if (defined('PDF_SHOW_DUE_DATE') && PDF_SHOW_DUE_DATE) {
            $this->Cell(100, 5, '', 0, 0);
            $this->Cell(49, 5, $this->convertEncoding(__('pdf_due_date')) . ':', 0, 0);
            $this->Cell(0, 5, date('d.m.Y', strtotime($this->invoice['due_date'])), 0, 1);
        }
        
        $this->Ln(10);
        
        // Überschrift
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, $this->convertEncoding(__('pdf_invoice')), 0, 1);
        $this->Ln(5);
        
        // Tabellenkopf
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(10, 7, $this->convertEncoding(__('position')), 0, 0, 'C', true);
        $this->Cell(70, 7, $this->convertEncoding(__('description')), 0, 0, 'L', true);
        $this->Cell(18, 7, $this->convertEncoding(__('quantity')), 0, 0, 'C', true);
        $this->Cell(28, 7, $this->convertEncoding(__('unit_price')), 0, 0, 'R', true);
        $this->Cell(16, 7, $this->convertEncoding(__('tax_rate')), 0, 0, 'C', true);
        $this->Cell(28, 7, $this->convertEncoding(__('total')), 0, 1, 'R', true);
        
        // Untere Linie unter Header
        $this->SetDrawColor(200, 200, 200);
        $currentY = $this->GetY();
        $this->Line(20, $currentY, 190, $currentY);
        $this->SetDrawColor(0, 0, 0);
        
        // Positionen
        $this->SetFont('Arial', '', 9);
        foreach ($this->items as $item) {
            // Höhe für diese Zeile berechnen (abhängig von Beschreibungslänge)
            $descriptionWidth = 70; // Breite der Beschreibungsspalte
            
            // Anzahl der Zeilen für die Beschreibung berechnen
            $nbLines = $this->NbLines($descriptionWidth, $this->convertEncoding($item['description']));
            $lineHeight = 5;
            $cellHeight = max(6, $nbLines * $lineHeight);
            
            // Y-Position vor der Zeile speichern
            $x = $this->GetX();
            $y = $this->GetY();
            
            // Position
            $this->Cell(10, $cellHeight, $item['position'], 0, 0, 'C');
            
            // Beschreibung mit MultiCell (erlaubt Umbruch)
            $this->MultiCell($descriptionWidth, $lineHeight, $this->convertEncoding($item['description']), 0, 'L');
            
            // Zurück zur Ausgangshöhe für die anderen Spalten
            $this->SetXY($x + 10 + $descriptionWidth, $y);
            
            // Menge
            $this->Cell(18, $cellHeight, number_format($item['quantity'], 2, ',', '.'), 0, 0, 'C');
            
            // Einzelpreis
            $this->Cell(28, $cellHeight, number_format($item['unit_price'], 2, ',', '.') . ' ' . CURRENCY, 0, 0, 'R');
            
            // Steuersatz
            $this->Cell(16, $cellHeight, number_format($item['tax_rate'], 0) . '%', 0, 0, 'C');
            
            // Gesamt
            $this->Cell(28, $cellHeight, number_format($item['total'], 2, ',', '.') . ' ' . CURRENCY, 0, 1, 'R');
        }
        
        // Summen
        $this->Ln(2);
        $this->SetFont('Arial', '', 9);
        $this->Cell(116, 6, '', 0, 0);
        $this->Cell(28, 6, $this->convertEncoding(__('subtotal')) . ':', 0, 0, 'R');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(26, 6, number_format($this->invoice['subtotal'], 2, ',', '.') . ' ' . CURRENCY, 0, 1, 'R');
        
        $this->SetFont('Arial', '', 9);
        $this->Cell(116, 6, '', 0, 0);
        $this->Cell(28, 6, $this->convertEncoding(__('tax_amount')) . ' (' . number_format($this->invoice['tax_rate'], 0) . '%):', 0, 0, 'R');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(26, 6, number_format($this->invoice['tax_amount'], 2, ',', '.') . ' ' . CURRENCY, 0, 1, 'R');
        
        $this->SetDrawColor(0, 0, 0);
        $this->Line(116, $this->GetY(), 190, $this->GetY());
        $this->Ln(1);
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(116, 7, '', 0, 0);
        $this->Cell(28, 7, $this->convertEncoding(__('total_amount')) . ':', 0, 0, 'R');
        $this->Cell(26, 7, number_format($this->invoice['total_amount'], 2, ',', '.') . ' ' . CURRENCY, 0, 1, 'R');
        
        // Zahlungshinweise
        if (!empty($this->invoice['payment_terms'])) {
            $this->Ln(10);
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(0, 5, $this->convertEncoding($this->invoice['payment_terms']));
        }
        
        // Notizen
        /*if (!empty($this->invoice['notes'])) {
            $this->Ln(5);
            $this->SetFont('Arial', 'I', 9);
            $this->MultiCell(0, 5, $this->convertEncoding('Hinweis: ' . $this->invoice['notes']));
        }*/
    }
}

class PDFGenerator {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * PDF für Rechnung erstellen
     * 
     * @param int $invoiceId Rechnungs-ID
     * @param string $output Ausgabemodus: 'I' = inline, 'D' = download, 'F' = file, 'S' = string
     * @param string $filePath Optional: Dateipfad für 'F' Modus
     * @return mixed Bei 'S' Modus wird PDF-String zurückgegeben, sonst void
     */
    public function generateInvoicePDF($invoiceId, $output = 'I', $filePath = null) {
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
            'customer_number' => $invoice['customer_number'],
            'vat_id' => $invoice['vat_id']
        ];
        
        // Firmendaten
        $stmt = $this->db->query("SELECT * FROM company_settings LIMIT 1");
        $company = $stmt->fetch();
        
        // PDF erstellen
        $pdf = new InvoicePDF();
        $pdf->setInvoiceData($invoice, $customer, $items, $company);
        $pdf->createInvoice();
        
        // Ausgeben oder speichern
        if ($output === 'F' && $filePath) {
            // Als Datei speichern
            return $pdf->Output($output, $filePath);
        } else {
            // Standard-Dateiname für Download/Inline
            $filename = (defined('PDF_FILENAME_SUFFIX') ? PDF_FILENAME_SUFFIX : 'Rechnung_') . $invoice['invoice_number'] . '.pdf';
            return $pdf->Output($output, $filename);
        }
    }
}
