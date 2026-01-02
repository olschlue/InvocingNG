<?php
/**
 * E-Mail-Klasse für InvoicingNG
 * Versendet E-Mails mit PDFs über SMTP
 */

class Email {
    private $host;
    private $port;
    private $username;
    private $password;
    private $from;
    private $fromName;
    private $encryption;
    
    public function __construct() {
        $this->host = SMTP_HOST;
        $this->port = SMTP_PORT;
        $this->username = SMTP_USER;
        $this->password = SMTP_PASS;
        $this->from = SMTP_FROM;
        $this->fromName = SMTP_FROM_NAME;
        $this->encryption = SMTP_ENCRYPTION;
    }
    
    /**
     * Sendet eine E-Mail
     * 
     * @param string $to Empfänger-E-Mail
     * @param string $subject Betreff
     * @param string $body Nachrichtentext
     * @param array $attachments Array mit Dateianhängen ['path' => 'Pfad', 'name' => 'Dateiname']
     * @return bool Erfolg
     */
    public function send($to, $subject, $body, $attachments = []) {
        // Boundary für Multipart-E-Mail
        $boundary = md5(uniqid(time()));
        
        // Headers
        $headers = [];
        $headers[] = "From: {$this->fromName} <{$this->from}>";
        $headers[] = "Reply-To: {$this->from}";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: multipart/mixed; boundary=\"{$boundary}\"";
        
        // E-Mail-Body zusammenstellen
        $message = "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $body . "\r\n\r\n";
        
        // Anhänge hinzufügen
        foreach ($attachments as $attachment) {
            if (file_exists($attachment['path'])) {
                $fileContent = chunk_split(base64_encode(file_get_contents($attachment['path'])));
                $message .= "--{$boundary}\r\n";
                $message .= "Content-Type: application/pdf; name=\"{$attachment['name']}\"\r\n";
                $message .= "Content-Disposition: attachment; filename=\"{$attachment['name']}\"\r\n";
                $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $message .= $fileContent . "\r\n\r\n";
            }
        }
        
        $message .= "--{$boundary}--";
        
        // E-Mail über SMTP senden
        try {
            if ($this->encryption === 'ssl') {
                $socket = @fsockopen("ssl://{$this->host}", $this->port, $errno, $errstr, 30);
            } else {
                $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 30);
            }
            
            if (!$socket) {
                error_log("SMTP Connection Error: {$errstr} ({$errno})");
                return false;
            }
            
            $this->getResponse($socket);
            
            // EHLO
            fputs($socket, "EHLO {$this->host}\r\n");
            $this->getResponse($socket);
            
            // STARTTLS wenn TLS verwendet wird
            if ($this->encryption === 'tls') {
                fputs($socket, "STARTTLS\r\n");
                $this->getResponse($socket);
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                fputs($socket, "EHLO {$this->host}\r\n");
                $this->getResponse($socket);
            }
            
            // AUTH LOGIN
            fputs($socket, "AUTH LOGIN\r\n");
            $this->getResponse($socket);
            
            fputs($socket, base64_encode($this->username) . "\r\n");
            $this->getResponse($socket);
            
            fputs($socket, base64_encode($this->password) . "\r\n");
            $this->getResponse($socket);
            
            // MAIL FROM
            fputs($socket, "MAIL FROM: <{$this->from}>\r\n");
            $this->getResponse($socket);
            
            // RCPT TO
            fputs($socket, "RCPT TO: <{$to}>\r\n");
            $this->getResponse($socket);
            
            // DATA
            fputs($socket, "DATA\r\n");
            $this->getResponse($socket);
            
            // Headers und Message
            fputs($socket, "To: {$to}\r\n");
            fputs($socket, "Subject: {$subject}\r\n");
            fputs($socket, implode("\r\n", $headers) . "\r\n\r\n");
            fputs($socket, $message . "\r\n.\r\n");
            $this->getResponse($socket);
            
            // QUIT
            fputs($socket, "QUIT\r\n");
            $this->getResponse($socket);
            
            fclose($socket);
            return true;
            
        } catch (Exception $e) {
            error_log("SMTP Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Liest die Antwort vom SMTP-Server
     */
    private function getResponse($socket) {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }
        return $response;
    }
    
    /**
     * Sendet eine Rechnung per E-Mail
     * 
     * @param int $invoiceId Rechnungs-ID
     * @param string $recipientEmail Empfänger-E-Mail (optional, verwendet Kunden-E-Mail wenn nicht angegeben)
     * @param string $message Zusätzliche Nachricht (optional)
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendInvoice($invoiceId, $recipientEmail = null, $message = '') {
        $invoiceObj = new Invoice();
        $invoice = $invoiceObj->getById($invoiceId);
        
        if (!$invoice) {
            return ['success' => false, 'message' => __('error_invoice_not_found')];
        }
        
        // E-Mail-Adresse bestimmen
        $to = $recipientEmail ?: $invoice['email'];
        
        if (empty($to)) {
            return ['success' => false, 'message' => __('error_no_email')];
        }
        
        // PDF generieren
        $pdfGenerator = new PDFGenerator();
        $pdfPath = TEMP_DIR . '/' . $invoice['invoice_number'] . '.pdf';
        
        // Stelle sicher, dass das TEMP-Verzeichnis existiert
        if (!is_dir(TEMP_DIR)) {
            if (!mkdir(TEMP_DIR, 0755, true)) {
                error_log("Failed to create TEMP_DIR: " . TEMP_DIR);
                return ['success' => false, 'message' => __('error_pdf_generation') . ' (Verzeichnis konnte nicht erstellt werden)'];
            }
        }
        
        // Prüfen ob Verzeichnis beschreibbar ist
        if (!is_writable(TEMP_DIR)) {
            error_log("TEMP_DIR is not writable: " . TEMP_DIR);
            return ['success' => false, 'message' => __('error_pdf_generation') . ' (Verzeichnis nicht beschreibbar)'];
        }
        
        try {
            $pdfGenerator->generateInvoicePDF($invoiceId, 'F', $pdfPath);
        } catch (Exception $e) {
            error_log("PDF Generation Error: " . $e->getMessage());
            return ['success' => false, 'message' => __('error_pdf_generation') . ' (' . $e->getMessage() . ')'];
        }
        
        if (!file_exists($pdfPath)) {
            error_log("PDF file not created: " . $pdfPath);
            return ['success' => false, 'message' => __('error_pdf_generation') . ' (Datei wurde nicht erstellt)'];
        }
        
        if (filesize($pdfPath) === 0) {
            error_log("PDF file is empty: " . $pdfPath);
            @unlink($pdfPath);
            return ['success' => false, 'message' => __('error_pdf_generation') . ' (Datei ist leer)'];
        }
        
        // E-Mail-Betreff und -Text
        $customerName = $invoice['company_name'] ?: ($invoice['first_name'] . ' ' . $invoice['last_name']);
        $subject = __('email_invoice_subject') . ' ' . $invoice['invoice_number'];
        
        $body = __('email_greeting') . ' ' . $customerName . ',<br><br>';
        
        if (!empty($message)) {
            $body .= nl2br(htmlspecialchars($message)) . '<br><br>';
        } else {
            $body .= __('email_invoice_body') . '<br><br>';
        }
        
        $body .= __('email_invoice_details') . ':<br>';
        $body .= __('invoice_number') . ': ' . htmlspecialchars($invoice['invoice_number']) . '<br>';
        $body .= __('invoice_date') . ': ' . date('d.m.Y', strtotime($invoice['invoice_date'])) . '<br>';
        $body .= __('total_amount') . ': ' . number_format($invoice['total_amount'], 2, ',', '.') . ' ' . CURRENCY . '<br>';
        $body .= __('due_date') . ': ' . date('d.m.Y', strtotime($invoice['due_date'])) . '<br><br>';
        
        $body .= __('email_invoice_attached') . '<br><br>';
        $body .= __('email_regards') . '<br>';
        $body .= $this->fromName;
        
        // E-Mail senden
        $attachments = [
            [
                'path' => $pdfPath,
                'name' => $invoice['invoice_number'] . '.pdf'
            ]
        ];
        
        $result = $this->send($to, $subject, $body, $attachments);
        
        // Temporäre PDF-Datei löschen
        @unlink($pdfPath);
        
        if ($result) {
            // Status auf "sent" setzen
            $invoiceObj->updateStatus($invoiceId, 'sent');
            return ['success' => true, 'message' => __('email_sent_success')];
        } else {
            return ['success' => false, 'message' => __('error_email_send')];
        }
    }
}
