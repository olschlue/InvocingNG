<?php
require_once '../config/config.php';

// Seite ermitteln
$page = $_GET['page'] ?? 'dashboard';

// Header einbinden
include 'includes/header.php';

// Entsprechende Seite laden
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
    case 'invoice_pdf':
        include 'pages/invoice_pdf.php';
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

// Footer einbinden
include 'includes/footer.php';
