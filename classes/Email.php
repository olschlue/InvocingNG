<?php
/**
 * E-Mail-Klasse für InvoicingNG
 * Versendet E-Mails mit PDFs über SMTP
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
    private $host;
    private $port;
    private $username;
    private $password;
    private $from;
    private $fromName;
    private $encryption;
    private $lastError = '';
    
    public function __construct() {
        // Verwende Einstellungen aus Datenbank, falls vorhanden, sonst aus config.php
        $this->host = defined('SMTP_HOST_DB') ? SMTP_HOST_DB : SMTP_HOST;
        $this->port = defined('SMTP_PORT_DB') ? SMTP_PORT_DB : SMTP_PORT;
        $this->username = defined('SMTP_USER_DB') ? SMTP_USER_DB : SMTP_USER;
        $this->password = defined('SMTP_PASS_DB') ? SMTP_PASS_DB : SMTP_PASS;
        $this->from = defined('SMTP_FROM_DB') ? SMTP_FROM_DB : SMTP_FROM;
        $this->fromName = defined('SMTP_FROM_NAME_DB') ? SMTP_FROM_NAME_DB : SMTP_FROM_NAME;
        $this->encryption = defined('SMTP_ENCRYPTION_DB') ? SMTP_ENCRYPTION_DB : SMTP_ENCRYPTION;
    }
    
    /**
     * Gibt den letzten Fehler zurück
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Sendet eine E-Mail mit PHPMailer
     * 
     * @param string $to Empfänger-E-Mail
     * @param string $subject Betreff
     * @param string $body Nachrichtentext (HTML)
     * @param array $attachments Array mit Dateianhängen ['path' => 'Pfad', 'name' => 'Dateiname']
     * @return bool Erfolg
     */
    public function send($to, $subject, $body, $attachments = []) {
        $mail = new PHPMailer(true);
        
        try {
            // Server-Einstellungen
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = $this->encryption === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->port;
            $mail->CharSet = 'UTF-8';
            
            // Debug-Modus aktivieren
            $mail->SMTPDebug = 2; // Detaillierte Debug-Ausgabe
            $mail->Debugoutput = function($str, $level) {
                error_log("SMTP Debug [$level]: $str");
            };
            
            // Timeout erhöhen für langsame Server
            $mail->Timeout = 30;
            $mail->SMTPKeepAlive = true;
            
            // Empfänger
            $mail->setFrom($this->from, $this->fromName);
            $mail->addAddress($to);
            $mail->addReplyTo($this->from, $this->fromName);
            
            // Inhalt
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags(str_replace('<br>', "\n", $body));
            
            // Anhänge hinzufügen
            foreach ($attachments as $attachment) {
                if (file_exists($attachment['path'])) {
                    $mail->addAttachment($attachment['path'], $attachment['name']);
                }
            }
            
            error_log("Attempting to send email to: $to via " . $this->host . ":" . $this->port);
            $mail->send();
            error_log("Email sent successfully to: $to");
            return true;
            
        } catch (Exception $e) {
            $this->lastError = "PHPMailer Error: " . $mail->ErrorInfo . " | Exception: " . $e->getMessage();
            error_log($this->lastError);
            error_log("Full trace: " . $e->getTraceAsString());
            return false;
        }
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
        $customerName = $invoice['last_name'];
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
            
            // Versandzeitstempel zur Notiz hinzufügen
            $timestamp = date('d.m.Y H:i:s');
            $noteAddition = "Versendet am " . $timestamp;
            $appendResult = $invoiceObj->appendNote($invoiceId, $noteAddition);
            error_log("Note appended to invoice $invoiceId: " . ($appendResult ? 'success' : 'failed'));
            
            return ['success' => true, 'message' => __('email_sent_success')];
        } else {
            error_log("Email sending failed for invoice $invoiceId");
            return ['success' => false, 'message' => __('error_email_send') . ': ' . $this->getLastError()];
        }
    }
}
