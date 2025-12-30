<?php
require_once '../config/config.php';

// Seite ermitteln
$page = $_GET['page'] ?? 'dashboard';

// PDF-Seite hat keinen Header/Footer
if ($page === 'invoice_pdf') {
    include 'pages/invoice_pdf.php';
    exit;
}

// Output-Buffering starten, um Redirects vor der Ausgabe zu ermöglichen
ob_start();

// Entsprechende Seite laden (kann Redirects enthalten)
switch ($page) {
    case 'customers':
        include 'pages/customers.php';
        break;
    case 'customer_edit':
        include 'pages/customer_edit.php';
        break;
    case 'invoices':
        include 'pages/invoices.php';
        break;
    case 'invoice_edit':
        include 'pages/invoice_edit.php';
        break;
    case 'payments':
        include 'pages/payments.php';
        break;
    case 'payment_edit':
        include 'pages/payment_edit.php';
        break;
    case 'dashboard':
    default:
        include 'pages/dashboard.php';
        break;
}

// Seiten-Inhalt zwischenspeichern
$content = ob_get_clean();

// Jetzt Header einbinden
include 'includes/header.php';

// Inhalt ausgeben
echo $content;

// Footer einbinden
include 'includes/footer.php';
