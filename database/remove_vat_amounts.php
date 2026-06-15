<?php
/**
 * Entfernt gespeicherte Umsatzsteuerwerte aus der Datenbank.
 *
 * Wirkung:
 * - invoices.tax_rate = 0
 * - invoices.tax_amount = 0
 * - invoices.total_amount = invoices.subtotal
 * - invoice_items.tax_rate = 0
 *
 * Verwendung:
 *   php database/remove_vat_amounts.php
 *   php database/remove_vat_amounts.php 123
 *   php database/remove_vat_amounts.php --invoice-id=123
 */

require_once __DIR__ . '/../config/config.php';

function parseTargetInvoiceId(array $argv): ?int
{
    foreach ($argv as $argument) {
        if (preg_match('/^--invoice-id=(\d+)$/', $argument, $matches)) {
            return (int) $matches[1];
        }
    }

    if (isset($argv[1]) && ctype_digit((string) $argv[1])) {
        return (int) $argv[1];
    }

    return null;
}

try {
    $db = Database::getInstance()->getConnection();
    $targetInvoiceId = parseTargetInvoiceId($argv ?? []);

    echo "Starte Entfernung der Umsatzsteuerwerte...\n";

    $db->beginTransaction();

    if ($targetInvoiceId !== null) {
        $itemUpdateStmt = $db->prepare("UPDATE invoice_items SET tax_rate = 0.00 WHERE invoice_id = ?");
        $itemUpdateStmt->execute([$targetInvoiceId]);
        $updatedItems = $itemUpdateStmt->rowCount();

        $invoiceUpdateStmt = $db->prepare("\n            UPDATE invoices\n            SET tax_rate = 0.00,\n                tax_amount = 0.00,\n                total_amount = subtotal\n            WHERE id = ?\n        ");
        $invoiceUpdateStmt->execute([$targetInvoiceId]);
        $updatedInvoices = $invoiceUpdateStmt->rowCount();

        echo "Betroffene Rechnung: {$targetInvoiceId}\n";
    } else {
        $updatedItems = $db->exec("UPDATE invoice_items SET tax_rate = 0.00");
        $updatedInvoices = $db->exec("\n            UPDATE invoices\n            SET tax_rate = 0.00,\n                tax_amount = 0.00,\n                total_amount = subtotal\n        ");

        echo "Betroffene Rechnungen: alle\n";
    }

    $db->commit();

    echo "✓ Positionen aktualisiert: " . (int) $updatedItems . "\n";
    echo "✓ Rechnungen aktualisiert: " . (int) $updatedInvoices . "\n";
    echo "\nFertig. Umsatzsteuerwerte wurden entfernt.\n";
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    echo "\n✗ Fehler bei der Aktualisierung:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
