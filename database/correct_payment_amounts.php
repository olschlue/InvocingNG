<?php
/**
 * Passt gespeicherte Zahlungsbeträge an den aktuellen Rechnungswert an.
 *
 * Standardverhalten:
 *   - Für jede Zahlung wird amount auf den aktuellen total_amount der zugehörigen Rechnung gesetzt.
 *
 * Verwendung:
 *   php database/correct_payment_amounts.php
 *   php database/correct_payment_amounts.php 123
 *   php database/correct_payment_amounts.php --payment-id=123
 *   php database/correct_payment_amounts.php --invoice-id=123
 */

require_once __DIR__ . '/../config/config.php';

function parseTargetIds(array $argv): array
{
    $targetPaymentId = null;
    $targetInvoiceId = null;

    foreach ($argv as $argument) {
        if (preg_match('/^--payment-id=(\d+)$/', $argument, $matches)) {
            $targetPaymentId = (int) $matches[1];
        }

        if (preg_match('/^--invoice-id=(\d+)$/', $argument, $matches)) {
            $targetInvoiceId = (int) $matches[1];
        }
    }

    if (isset($argv[1]) && ctype_digit((string) $argv[1])) {
        $targetPaymentId = (int) $argv[1];
    }

    return [$targetPaymentId, $targetInvoiceId];
}

try {
    $db = Database::getInstance()->getConnection();
    [$targetPaymentId, $targetInvoiceId] = parseTargetIds($argv ?? []);

    echo "Starte Korrektur der Zahlungsbeträge...\n";

    $sql = "
        SELECT p.id AS payment_id,
               p.invoice_id,
               p.amount AS payment_amount,
               p.payment_date,
               p.payment_method,
               p.reference,
               i.invoice_number,
               i.total_amount AS invoice_total
        FROM payments p
        INNER JOIN invoices i ON i.id = p.invoice_id
        WHERE 1 = 1
    ";
    $params = [];

    if ($targetPaymentId !== null) {
        $sql .= " AND p.id = ?";
        $params[] = $targetPaymentId;
    }

    if ($targetInvoiceId !== null) {
        $sql .= " AND p.invoice_id = ?";
        $params[] = $targetInvoiceId;
    }

    $stmt = $db->prepare($sql . " ORDER BY p.id ASC");
    $stmt->execute($params);
    $payments = $stmt->fetchAll();

    if (empty($payments)) {
        echo "Keine Zahlungen gefunden.\n";
        exit(0);
    }

    $updateStmt = $db->prepare("
        UPDATE payments
        SET amount = ?
        WHERE id = ?
    ");

    $statusStmt = $db->prepare("
        SELECT COALESCE(SUM(amount), 0) AS total_paid
        FROM payments
        WHERE invoice_id = ?
    ");

    $invoiceStmt = $db->prepare("
        SELECT total_amount
        FROM invoices
        WHERE id = ?
    ");

    $statusUpdateStmt = $db->prepare("
        UPDATE invoices
        SET status = ?
        WHERE id = ?
    ");

    $corrected = 0;
    $affectedInvoices = [];
    foreach ($payments as $payment) {
        $targetAmount = (float) $payment['invoice_total'];
        $currentAmount = (float) $payment['payment_amount'];
        $invoiceId = (int) $payment['invoice_id'];

        if ($currentAmount !== $targetAmount) {
            $updateStmt->execute([$targetAmount, $payment['payment_id']]);
            echo "✓ Zahlung {$payment['payment_id']} für Rechnung {$payment['invoice_number']} korrigiert: " . number_format($currentAmount, 2, ',', '.') . " -> " . number_format($targetAmount, 2, ',', '.') . "\n";
            $corrected++;
        } else {
            echo "= Zahlung {$payment['payment_id']} für Rechnung {$payment['invoice_number']} bereits korrekt\n";
        }

        $affectedInvoices[$invoiceId] = $payment['invoice_number'];
    }

    echo "\nAktualisiere Rechnungsstatus...\n";
    foreach ($affectedInvoices as $invoiceId => $invoiceNumber) {
        $invoiceStmt->execute([$invoiceId]);
        $invoiceTotal = (float) $invoiceStmt->fetchColumn();

        $statusStmt->execute([$invoiceId]);
        $totalPaid = (float) $statusStmt->fetchColumn();

        if ($totalPaid >= $invoiceTotal && $invoiceTotal > 0) {
            $newStatus = 'paid';
        } elseif ($totalPaid > 0) {
            $newStatus = 'sent';
        } else {
            $newStatus = 'draft';
        }

        $statusUpdateStmt->execute([$newStatus, $invoiceId]);
        echo "→ Rechnung {$invoiceNumber} ({$invoiceId}) Status: {$newStatus}\n";
    }

    echo "\nFertig. Korrigierte Zahlungen: {$corrected}\n";
} catch (PDOException $e) {
    echo "\n✗ Fehler bei der Korrektur:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
