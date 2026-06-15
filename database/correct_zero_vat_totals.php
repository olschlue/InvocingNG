<?php
/**
 * Berechnet gespeicherte Rechnungs-Gesamtsummen für alle Rechnungen neu.
 * Die Umsatzsteuer wird dabei immer auf 0 gesetzt.
 *
 * Verwendung:
 *   php database/correct_zero_vat_totals.php
 *   php database/correct_zero_vat_totals.php 123
 *   php database/correct_zero_vat_totals.php --invoice-id=123
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

    echo "Starte Korrektur aller Rechnungen...\n";

    $sql = "
        SELECT id, invoice_number, subtotal, tax_amount, total_amount
        FROM invoices
        WHERE 1 = 1
    ";
    $params = [];

    if ($targetInvoiceId !== null) {
        $sql .= " AND id = ?";
        $params[] = $targetInvoiceId;
    }

    $stmt = $db->prepare($sql . " ORDER BY id ASC");
    $stmt->execute($params);
    $invoices = $stmt->fetchAll();

    if (empty($invoices)) {
        echo "Keine Rechnungen gefunden.\n";
        exit(0);
    }

    $updateStmt = $db->prepare("
        UPDATE invoices
        SET subtotal = ?, tax_amount = ?, total_amount = ?
        WHERE id = ?
    ");

    $corrected = 0;
    foreach ($invoices as $invoice) {
        $itemsStmt = $db->prepare("
            SELECT COALESCE(SUM(total), 0) AS subtotal,
                   COALESCE(SUM(total * tax_rate / 100), 0) AS tax_amount
            FROM invoice_items
            WHERE invoice_id = ?
        ");
        $itemsStmt->execute([$invoice['id']]);
        $itemTotals = $itemsStmt->fetch();
        $subtotal = (float) ($itemTotals['subtotal'] ?? 0);
        $taxAmount = 0.0;
        $totalAmount = $subtotal;

        $changed = ((float) $invoice['subtotal'] !== $subtotal)
            || ((float) $invoice['tax_amount'] !== $taxAmount)
            || ((float) $invoice['total_amount'] !== $totalAmount);

        if ($changed) {
            $updateStmt->execute([$subtotal, $taxAmount, $totalAmount, $invoice['id']]);
            echo "✓ Rechnung {$invoice['invoice_number']} ({$invoice['id']}) korrigiert: subtotal=" . number_format($subtotal, 2, ',', '.') . " tax=" . number_format($taxAmount, 2, ',', '.') . " total=" . number_format($totalAmount, 2, ',', '.') . "\n";
            $corrected++;
        } else {
            echo "= Rechnung {$invoice['invoice_number']} ({$invoice['id']}) bereits korrekt\n";
        }
    }

    echo "\nFertig. Korrigierte Rechnungen: {$corrected}\n";
} catch (PDOException $e) {
    echo "\n✗ Fehler bei der Korrektur:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
