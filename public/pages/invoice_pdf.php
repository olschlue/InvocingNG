<?php
require_once '../config/config.php';

$invoiceId = $_GET['id'] ?? null;

if (!$invoiceId) {
    die('Keine Rechnungs-ID angegeben');
}

// PDF generieren und direkt im Browser anzeigen
$pdfGenerator = new PDFGenerator();
$pdfGenerator->generateInvoicePDF($invoiceId, 'I');
