<?php
/**
 * Datenmigration von alter Datenbank zu InvoicingNG
 * 
 * ANLEITUNG:
 * 1. Passe die Verbindungsdaten für die alte Datenbank an
 * 2. Passe die Feldnamen und Tabellennamen an deine alte DB-Struktur an
 * 3. Führe das Skript aus: php migrate_from_old_db.php
 */

// Fehlerausgabe aktivieren
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Alte Datenbank-Konfiguration (ANPASSEN!)
define('OLD_DB_HOST', 'localhost');
define('OLD_DB_NAME', 'alte_datenbank');
define('OLD_DB_USER', 'root');
define('OLD_DB_PASS', '');

// Neue Datenbank-Konfiguration
require_once __DIR__ . '/../config/config.php';

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
    
    // Mapping für alte zu neue Feldnamen (ANPASSEN!)
    $customerFieldMapping = [
        // 'alter_feldname' => 'neuer_feldname'
        'id' => 'id',
        'kundennummer' => 'customer_number',
        'firma' => 'company_name',
        'vorname' => 'first_name',
        'nachname' => 'last_name',
        'email' => 'email',
        'telefon' => 'phone',
        'strasse' => 'address_street',
        'stadt' => 'address_city',
        'plz' => 'address_zip',
        'land' => 'address_country',
        'steuernummer' => 'tax_id',
        'notizen' => 'notes'
    ];
    
    // ===== KUNDEN MIGRIEREN =====
    echo "=== KUNDEN MIGRIEREN ===\n";
    
    // Alte Kunden auslesen (TABELLENNAME ANPASSEN!)
    $oldCustomers = $oldDb->query("SELECT * FROM alte_kunden_tabelle")->fetchAll(PDO::FETCH_ASSOC);
    echo "Gefunden: " . count($oldCustomers) . " Kunden\n";
    
    $customerIdMap = []; // Alte ID => Neue ID
    $migratedCustomers = 0;
    
    foreach ($oldCustomers as $oldCustomer) {
        try {
            // Kundennummer generieren falls nicht vorhanden
            $customerNumber = !empty($oldCustomer[$customerFieldMapping['kundennummer']]) 
                ? $oldCustomer[$customerFieldMapping['kundennummer']]
                : 'K' . str_pad($oldCustomer['id'], 5, '0', STR_PAD_LEFT);
            
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
                $oldCustomer['firma'] ?? null,
                $oldCustomer['vorname'] ?? null,
                $oldCustomer['nachname'] ?? null,
                $oldCustomer['email'] ?? null,
                $oldCustomer['telefon'] ?? null,
                $oldCustomer['strasse'] ?? null,
                $oldCustomer['stadt'] ?? null,
                $oldCustomer['plz'] ?? null,
                $oldCustomer['land'] ?? 'Deutschland',
                $oldCustomer['steuernummer'] ?? null,
                $oldCustomer['notizen'] ?? null
            ]);
            
            $customerIdMap[$oldCustomer['id']] = $newDb->lastInsertId();
            $migratedCustomers++;
            
        } catch (PDOException $e) {
            echo "✗ Fehler bei Kunde ID " . $oldCustomer['id'] . ": " . $e->getMessage() . "\n";
        }
    }
    
    echo "✓ $migratedCustomers Kunden migriert\n\n";
    
    // ===== RECHNUNGEN MIGRIEREN =====
    echo "=== RECHNUNGEN MIGRIEREN ===\n";
    
    // Alte Rechnungen auslesen (TABELLENNAME UND FELDER ANPASSEN!)
    $oldInvoices = $oldDb->query("SELECT * FROM alte_rechnungen_tabelle ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    echo "Gefunden: " . count($oldInvoices) . " Rechnungen\n";
    
    $invoiceIdMap = []; // Alte ID => Neue ID
    $migratedInvoices = 0;
    
    foreach ($oldInvoices as $oldInvoice) {
        try {
            // Kunden-ID mappen
            $newCustomerId = $customerIdMap[$oldInvoice['kunden_id']] ?? null;
            
            if (!$newCustomerId) {
                echo "✗ Kunde nicht gefunden für Rechnung ID " . $oldInvoice['id'] . "\n";
                continue;
            }
            
            // Status mappen (ANPASSEN!)
            $statusMap = [
                'entwurf' => 'draft',
                'versendet' => 'sent',
                'bezahlt' => 'paid',
                'überfällig' => 'overdue',
                'storniert' => 'cancelled'
            ];
            
            $status = $statusMap[$oldInvoice['status']] ?? 'draft';
            
            // Rechnung einfügen
            $stmt = $newDb->prepare("
                INSERT INTO invoices (
                    invoice_number, customer_id, invoice_date, service_date,
                    due_date, status, tax_rate, notes, payment_terms
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $oldInvoice['rechnungsnummer'] ?? 'RE-' . $oldInvoice['id'],
                $newCustomerId,
                $oldInvoice['rechnungsdatum'] ?? date('Y-m-d'),
                $oldInvoice['leistungsdatum'] ?? null,
                $oldInvoice['faelligkeitsdatum'] ?? date('Y-m-d', strtotime('+14 days')),
                $status,
                $oldInvoice['steuersatz'] ?? 19.00,
                $oldInvoice['notizen'] ?? null,
                $oldInvoice['zahlungsbedingungen'] ?? null
            ]);
            
            $newInvoiceId = $newDb->lastInsertId();
            $invoiceIdMap[$oldInvoice['id']] = $newInvoiceId;
            $migratedInvoices++;
            
            // ===== RECHNUNGSPOSITIONEN MIGRIEREN =====
            // Positionen für diese Rechnung auslesen (ANPASSEN!)
            $oldItems = $oldDb->prepare("SELECT * FROM alte_positionen_tabelle WHERE rechnungs_id = ? ORDER BY position ASC");
            $oldItems->execute([$oldInvoice['id']]);
            
            $position = 1;
            foreach ($oldItems->fetchAll(PDO::FETCH_ASSOC) as $oldItem) {
                $total = ($oldItem['menge'] ?? 1) * ($oldItem['einzelpreis'] ?? 0);
                
                $itemStmt = $newDb->prepare("
                    INSERT INTO invoice_items (
                        invoice_id, position, description, quantity, 
                        unit_price, tax_rate, total
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $itemStmt->execute([
                    $newInvoiceId,
                    $position++,
                    $oldItem['beschreibung'] ?? '',
                    $oldItem['menge'] ?? 1,
                    $oldItem['einzelpreis'] ?? 0,
                    $oldItem['steuersatz'] ?? 19.00,
                    $total
                ]);
            }
            
        } catch (PDOException $e) {
            echo "✗ Fehler bei Rechnung ID " . $oldInvoice['id'] . ": " . $e->getMessage() . "\n";
        }
    }
    
    echo "✓ $migratedInvoices Rechnungen migriert\n\n";
    
    // ===== ZAHLUNGEN MIGRIEREN =====
    echo "=== ZAHLUNGEN MIGRIEREN ===\n";
    
    // Alte Zahlungen auslesen (TABELLENNAME UND FELDER ANPASSEN!)
    $oldPayments = $oldDb->query("SELECT * FROM alte_zahlungen_tabelle ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    echo "Gefunden: " . count($oldPayments) . " Zahlungen\n";
    
    $migratedPayments = 0;
    
    foreach ($oldPayments as $oldPayment) {
        try {
            // Rechnungs-ID mappen
            $newInvoiceId = $invoiceIdMap[$oldPayment['rechnungs_id']] ?? null;
            
            if (!$newInvoiceId) {
                echo "✗ Rechnung nicht gefunden für Zahlung ID " . $oldPayment['id'] . "\n";
                continue;
            }
            
            // Zahlungsmethode mappen (ANPASSEN!)
            $methodMap = [
                'barzahlung' => 'cash',
                'überweisung' => 'bank_transfer',
                'kreditkarte' => 'credit_card',
                'paypal' => 'paypal'
            ];
            
            $method = $methodMap[$oldPayment['zahlungsart']] ?? 'bank_transfer';
            
            $stmt = $newDb->prepare("
                INSERT INTO payments (
                    invoice_id, payment_date, amount, payment_method, 
                    reference, notes
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $newInvoiceId,
                $oldPayment['zahlungsdatum'] ?? date('Y-m-d'),
                $oldPayment['betrag'] ?? 0,
                $method,
                $oldPayment['referenz'] ?? null,
                $oldPayment['notizen'] ?? null
            ]);
            
            $migratedPayments++;
            
        } catch (PDOException $e) {
            echo "✗ Fehler bei Zahlung ID " . $oldPayment['id'] . ": " . $e->getMessage() . "\n";
        }
    }
    
    echo "✓ $migratedPayments Zahlungen migriert\n\n";
    
    // ===== ZUSAMMENFASSUNG =====
    echo "=== MIGRATION ABGESCHLOSSEN ===\n";
    echo "Kunden:     $migratedCustomers\n";
    echo "Rechnungen: $migratedInvoices\n";
    echo "Zahlungen:  $migratedPayments\n";
    echo "\nBitte überprüfe die migrierten Daten in der neuen Datenbank.\n";
    
} catch (PDOException $e) {
    echo "✗ FEHLER: " . $e->getMessage() . "\n";
    exit(1);
}
