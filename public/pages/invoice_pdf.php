<?php
require_once '../config/config.php';

$invoiceId = $_GET['id'] ?? null;

if (!$invoiceId) {
    die(__('error_invoice_id_missing'));
}

// PDF generieren und direkt im Browser anzeigen
$pdfGenerator = new PDFGenerator();
$pdfGenerator->generateInvoicePDF($invoiceId, 'I');
