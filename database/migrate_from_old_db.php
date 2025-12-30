<?php
/**
 * Datenmigration von alter Datenbank zu InvoicingNG
 * Migriert Daten aus der alten addressbook/invoice Datenbank
 * 
 * ANLEITUNG:
 * 1. Passe die Verbindungsdaten für die alte Datenbank an (Zeilen 16-19)
 * 2. Rufe das Skript im Browser auf: /database/migrate_from_old_db.php
 */

// Fehlerausgabe aktivieren
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Alte Datenbank-Konfiguration (ANPASSEN!)
define('OLD_DB_HOST', 'db5004652185.hosting-data.io');
define('OLD_DB_NAME', 'dbs3895544');
define('OLD_DB_USER', 'dbu1361608');
define('OLD_DB_PASS', 'ee97mnee');

// Neue Datenbank-Konfiguration
require_once __DIR__ . '/../config/config.php';

// HTML Header
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datenmigration - InvoicingNG</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            border: 1px solid #28a745;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #dc3545;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .output {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            white-space: pre-wrap;
            max-height: 500px;
            overflow-y: auto;
        }
        .btn {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn:hover {
            background: #2980b9;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Datenmigration zu InvoicingNG</h1>
        
<?php
// Prüfen ob Migration gestartet werden soll
if (!isset($_POST['confirm_migration'])) {
    // Zeige Bestätigungsformular
    ?>
        <div class="warning">
            <strong>⚠️ WARNUNG:</strong> Diese Migration wird alle bestehenden Daten in der neuen Datenbank <strong>unwiderruflich löschen</strong>!
        </div>
        
        <div class="info">
            <h3>📋 Was wird migriert:</h3>
            <ul>
                <li><strong>Kunden</strong> aus der Tabelle <code>addressbook</code></li>
                <li><strong>Rechnungen</strong> aus der Tabelle <code>invoice</code></li>
                <li><strong>Rechnungspositionen</strong> aus der Tabelle <code>invoicepos</code></li>
                <li><strong>Zahlungen</strong> aus der Tabelle <code>payment</code></li>
            </ul>
            
            <h3>🗄️ Verbindungsdetails:</h3>
            <ul>
                <li><strong>Alte Datenbank:</strong> <?php echo OLD_DB_HOST . ' / ' . OLD_DB_NAME; ?></li>
                <li><strong>Neue Datenbank:</strong> <?php echo DB_HOST . ' / ' . DB_NAME; ?></li>
            </ul>
        </div>
        
        <form method="POST">
            <p><strong>Sind Sie sicher, dass Sie fortfahren möchten?</strong></p>
            <button type="submit" name="confirm_migration" value="yes" class="btn btn-danger">✓ Ja, Migration starten</button>
            <a href="/" class="btn btn-secondary">✗ Abbrechen</a>
        </form>
    <?php
} else {
    // Migration durchführen
    ?>
        <div class="info">
            <strong>⏳ Migration läuft...</strong> Bitte warten Sie, dies kann einige Minuten dauern.
        </div>
        
        <div class="output"><?php
    
    ob_start();
    
    echo "=== Datenmigration zu InvoicingNG ===\n\n";

try {
    // Verbindung zur alten Datenbank
    echo "Verbinde mit alter Datenbank...\n";
    $oldDb = new PDO(
        "mysql:host=" . OLD_DB_HOST . ";dbname=" . OLD_DB_NAME . ";charset=utf8mb4",
        OLD_DB_USER,
        OLD_DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✓ Verbindung zur alten Datenbank hergestellt\n\n";
    
    // Verbindung zur neuen Datenbank
    echo "Verbinde mit neuer Datenbank...\n";
    $newDb = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✓ Verbindung zur neuen Datenbank hergestellt\n\n";
    
    // ===== ALTE DATEN LÖSCHEN =====
    echo "=== ALTE DATEN LÖSCHEN ===\n";
    
    echo "Lösche bestehende Daten...\n";
    
    // Foreign Key Checks temporär deaktivieren
    $newDb->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Tabellen in korrekter Reihenfolge leeren (wegen Foreign Keys)
    $tables = ['payments', 'invoice_items', 'invoices', 'customers'];
    foreach ($tables as $table) {
        try {
            $newDb->exec("TRUNCATE TABLE $table");
            echo "✓ Tabelle '$table' geleert\n";
        } catch (PDOException $e) {
            echo "✗ Fehler beim Leeren von '$table': " . $e->getMessage() . "\n";
        }
    }
    
    // Foreign Key Checks wieder aktivieren
    $newDb->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "✓ Alle Daten gelöscht\n\n";
    
    // ===== KUNDEN MIGRIEREN =====
    echo "=== KUNDEN MIGRIEREN (addressbook) ===\n";
    
    // Alte Kunden auslesen
    $oldCustomers = $oldDb->query("
        SELECT * FROM addressbook 
        WHERE CANCELED = 0 OR CANCELED IS NULL
        ORDER BY MYID ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    echo "Gefunden: " . count($oldCustomers) . " Kunden\n";
    
    $customerIdMap = []; // Alte MYID => Neue ID
    $migratedCustomers = 0;
    
    foreach ($oldCustomers as $oldCustomer) {
        try {
            // Kundennummer generieren
            $customerNumber = 'K' . str_pad($oldCustomer['MYID'], 5, '0', STR_PAD_LEFT);
            
            // Vollständigen Namen zusammensetzen falls vorhanden
            $firstName = trim($oldCustomer['FIRSTNAME'] ?? '');
            $lastName = trim($oldCustomer['LASTNAME'] ?? '');
            $companyName = trim($oldCustomer['COMPANY'] ?? '');
            
            // Kunde in neue DB einfügen
            $stmt = $newDb->prepare("
                INSERT INTO customers (
                    customer_number, company_name, first_name, last_name, 
                    email, phone, address_street, address_city, address_zip, 
                    address_country, tax_id, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $customerNumber,
                $companyName ?: null,
                $firstName ?: null,
                $lastName ?: null,
                trim($oldCustomer['EMAIL'] ?? '') ?: null,
                trim($oldCustomer['PHONEWORK'] ?? $oldCustomer['PHONEOFFI'] ?? '') ?: null,
                trim($oldCustomer['ADDRESS'] ?? '') ?: null,
                trim($oldCustomer['CITY'] ?? '') ?: null,
                trim($oldCustomer['POSTALCODE'] ?? '') ?: null,
                trim($oldCustomer['COUNTRY'] ?? 'Deutschland') ?: 'Deutschland',
                trim($oldCustomer['TAXNR'] ?? '') ?: null,
                trim($oldCustomer['NOTE'] ?? '') ?: null
            ]);
            
            $customerIdMap[$oldCustomer['MYID']] = $newDb->lastInsertId();
            $migratedCustomers++;
            
        } catch (PDOException $e) {
            echo "✗ Fehler bei Kunde MYID " . $oldCustomer['MYID'] . ": " . $e->getMessage() . "\n";
        }
    }
    
    echo "✓ $migratedCustomers Kunden migriert\n\n";
    
    // ===== RECHNUNGEN MIGRIEREN =====
    echo "=== RECHNUNGEN MIGRIEREN (invoice) ===\n";
    
    // Alte Rechnungen auslesen
    $oldInvoices = $oldDb->query("
        SELECT * FROM invoice 
        WHERE CANCELED = 0 OR CANCELED IS NULL
        ORDER BY INVOICEID ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    echo "Gefunden: " . count($oldInvoices) . " Rechnungen\n";
    
    $invoiceIdMap = []; // Alte INVOICEID => Neue ID
    $migratedInvoices = 0;
    $skippedInvoices = 0;
    
    foreach ($oldInvoices as $oldInvoice) {
        try {
            // Kunden-ID mappen
            $newCustomerId = $customerIdMap[$oldInvoice['MYID']] ?? null;
            
            if (!$newCustomerId) {
                echo "✗ Kunde nicht gefunden für Rechnung INVOICEID " . $oldInvoice['INVOICEID'] . " (MYID: " . $oldInvoice['MYID'] . ")\n";
                $skippedInvoices++;
                continue;
            }
            
            // Status ermitteln
            if (!empty($oldInvoice['CANCELED']) && $oldInvoice['CANCELED'] > 0) {
                $status = 'cancelled';
            } elseif (!empty($oldInvoice['PAID']) && $oldInvoice['PAID'] > 0) {
                $status = 'paid';
            } elseif (!empty($oldInvoice['INVOICE_MAILED']) || !empty($oldInvoice['INVOICE_PRINTED'])) {
                $status = 'sent';
            } else {
                $status = 'draft';
            }
            
            // Rechnungsnummer generieren
            $invoiceNumber = date('Y', strtotime($oldInvoice['INVOICE_DATE'])) . '-' . $oldInvoice['INVOICEID'];
            
            // Fälligkeitsdatum berechnen (falls nicht vorhanden, +14 Tage)
            $dueDate = !empty($oldInvoice['METHOD_OF_PAY_DATE']) && $oldInvoice['METHOD_OF_PAY_DATE'] != '0000-00-00'
                ? $oldInvoice['METHOD_OF_PAY_DATE']
                : date('Y-m-d', strtotime($oldInvoice['INVOICE_DATE'] . ' +14 days'));
            
            // Leistungsdatum (ACHIEVED_DATE)
            $serviceDate = !empty($oldInvoice['ACHIEVED_DATE']) && $oldInvoice['ACHIEVED_DATE'] != '0000-00-00'
                ? $oldInvoice['ACHIEVED_DATE']
                : null;
            
            // Rechnung einfügen
            $stmt = $newDb->prepare("
                INSERT INTO invoices (
                    invoice_number, customer_id, invoice_date, service_date,
                    due_date, status, tax_rate, notes, payment_terms
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $invoiceNumber,
                $newCustomerId,
                $oldInvoice['INVOICE_DATE'],
                $serviceDate,
                $dueDate,
                $status,
                19.00, // Standard-Steuersatz
                trim($oldInvoice['NOTE'] ?? '') ?: null,
                trim($oldInvoice['MESSAGE_DESC'] ?? '') ?: 'Bitte überweisen Sie den Betrag innerhalb von 14 Tagen.'
            ]);
            
            $newInvoiceId = $newDb->lastInsertId();
            $invoiceIdMap[$oldInvoice['INVOICEID']] = $newInvoiceId;
            $migratedInvoices++;
            
            // ===== RECHNUNGSPOSITIONEN MIGRIEREN =====
            $oldItems = $oldDb->prepare("
                SELECT * FROM invoicepos 
                WHERE INVOICEID = ? 
                ORDER BY INVOICEPOSID ASC
            ");
            $oldItems->execute([$oldInvoice['INVOICEID']]);
            
            $position = 1;
            foreach ($oldItems->fetchAll(PDO::FETCH_ASSOC) as $oldItem) {
                // Steuersatz aus TAX ermitteln (TAX ist ID in tax-Tabelle)
                $taxRate = 19.00; // Standard
                if (!empty($oldItem['TAX_MULTI'])) {
                    // TAX_MULTI ist z.B. 1.19 für 19% MwSt
                    $taxRate = ($oldItem['TAX_MULTI'] - 1) * 100;
                }
                
                $quantity = floatval($oldItem['POS_QUANTITY'] ?? 1);
                $unitPrice = floatval($oldItem['POS_PRICE'] ?? 0);
                $total = $quantity * $unitPrice;
                
                $itemStmt = $newDb->prepare("
                    INSERT INTO invoice_items (
                        invoice_id, position, description, quantity, 
                        unit_price, tax_rate, total
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $itemStmt->execute([
                    $newInvoiceId,
                    $position++,
                    trim($oldItem['POS_DESC'] ?? ''),
                    $quantity,
                    $unitPrice,
                    $taxRate,
                    $total
                ]);
            }
            
        } catch (PDOException $e) {
            echo "✗ Fehler bei Rechnung INVOICEID " . $oldInvoice['INVOICEID'] . ": " . $e->getMessage() . "\n";
            $skippedInvoices++;
        }
    }
    
    echo "✓ $migratedInvoices Rechnungen migriert";
    if ($skippedInvoices > 0) {
        echo " ($skippedInvoices übersprungen)";
    }
    echo "\n\n";
    
    // ===== ZAHLUNGEN MIGRIEREN =====
    echo "=== ZAHLUNGEN MIGRIEREN (payment) ===\n";
    
    // Alte Zahlungen auslesen
    $oldPayments = $oldDb->query("
        SELECT * FROM payment 
        WHERE (CANCELED = 0 OR CANCELED IS NULL) AND SUM_PAID > 0
        ORDER BY PAYMENTID ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    echo "Gefunden: " . count($oldPayments) . " Zahlungen\n";
    
    $migratedPayments = 0;
    $skippedPayments = 0;
    
    foreach ($oldPayments as $oldPayment) {
        try {
            // Rechnungs-ID mappen
            $newInvoiceId = $invoiceIdMap[$oldPayment['INVOICEID']] ?? null;
            
            if (!$newInvoiceId) {
                echo "✗ Rechnung nicht gefunden für Zahlung PAYMENTID " . $oldPayment['PAYMENTID'] . " (INVOICEID: " . $oldPayment['INVOICEID'] . ")\n";
                $skippedPayments++;
                continue;
            }
            
            // Zahlungsmethode ermitteln
            $methodOfPay = strtolower(trim($oldPayment['METHOD_OF_PAY'] ?? ''));
            $method = 'bank_transfer'; // Standard
            
            if (strpos($methodOfPay, 'bar') !== false || strpos($methodOfPay, 'cash') !== false) {
                $method = 'cash';
            } elseif (strpos($methodOfPay, 'karte') !== false || strpos($methodOfPay, 'card') !== false) {
                $method = 'credit_card';
            } elseif (strpos($methodOfPay, 'paypal') !== false) {
                $method = 'paypal';
            } elseif (strpos($methodOfPay, 'überweisung') !== false || strpos($methodOfPay, 'transfer') !== false) {
                $method = 'bank_transfer';
            }
            
            $stmt = $newDb->prepare("
                INSERT INTO payments (
                    invoice_id, payment_date, amount, payment_method, 
                    reference, notes
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $newInvoiceId,
                $oldPayment['PAYMENT_DATE'] != '0000-00-00' ? $oldPayment['PAYMENT_DATE'] : date('Y-m-d'),
                floatval($oldPayment['SUM_PAID'] ?? 0),
                $method,
                trim($oldPayment['METHOD_OF_PAY'] ?? '') ?: null,
                trim($oldPayment['NOTE'] ?? '') ?: null
            ]);
            
            $migratedPayments++;
            
        } catch (PDOException $e) {
            echo "✗ Fehler bei Zahlung PAYMENTID " . $oldPayment['PAYMENTID'] . ": " . $e->getMessage() . "\n";
            $skippedPayments++;
        }
    }
    
    echo "✓ $migratedPayments Zahlungen migriert";
    if ($skippedPayments > 0) {
        echo " ($skippedPayments übersprungen)";
    }
    echo "\n\n";
    
    // ===== ZUSAMMENFASSUNG =====
    echo "=== MIGRATION ABGESCHLOSSEN ===\n";
    echo "Kunden:     $migratedCustomers\n";
    echo "Rechnungen: $migratedInvoices\n";
    echo "Zahlungen:  $migratedPayments\n";
    echo "\nBitte überprüfe die migrierten Daten in der neuen Datenbank.\n";
    
    $output = ob_get_clean();
    echo htmlspecialchars($output);
    
    ?>
        </div>
        
        <div class="success">
            <strong>✅ Migration erfolgreich abgeschlossen!</strong><br>
            <ul>
                <li>Kunden: <?php echo $migratedCustomers; ?></li>
                <li>Rechnungen: <?php echo $migratedInvoices; ?></li>
                <li>Zahlungen: <?php echo $migratedPayments; ?></li>
            </ul>
        </div>
        
        <a href="/" class="btn">← Zurück zum Dashboard</a>
    <?php
    
} catch (PDOException $e) {
    $output = ob_get_clean();
    echo htmlspecialchars($output);
    
    ?>
        </div>
        
        <div class="error">
            <strong>❌ FEHLER:</strong> <?php echo htmlspecialchars($e->getMessage()); ?>
        </div>
        
        <a href="/database/migrate_from_old_db.php" class="btn btn-secondary">← Zurück</a>
    <?php
}
}
?>
    </div>
</body>
</html>
