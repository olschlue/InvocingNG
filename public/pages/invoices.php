<?php
$invoiceObj = new Invoice();

// Rechnung kopieren
if (isset($_GET['action']) && $_GET['action'] === 'copy' && isset($_GET['id'])) {
    $newInvoiceId = $invoiceObj->duplicate($_GET['id']);
    if ($newInvoiceId) {
        header('Location: ?page=invoice_edit&id=' . $newInvoiceId);
        exit;
    }
}

// Filter anwenden
$filter = $_GET['filter'] ?? 'all';
$invoices = $filter === 'overdue' ? $invoiceObj->getOverdue() : $invoiceObj->getAll($filter !== 'all' ? $filter : null);
?>

<div class="card">
    <h2>Rechnungsverwaltung</h2>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="?page=invoice_edit&action=new" class="btn btn-success">Neue Rechnung</a>
        
        <div style="display: flex; gap: 10px;">
            <a href="?page=invoices&filter=all" class="btn btn-small <?php echo $filter === 'all' ? '' : 'btn-secondary'; ?>">Alle</a>
            <a href="?page=invoices&filter=draft" class="btn btn-small">Entwurf</a>
            <a href="?page=invoices&filter=sent" class="btn btn-small">Versendet</a>
            <a href="?page=invoices&filter=paid" class="btn btn-small">Bezahlt</a>
            <a href="?page=invoices&filter=overdue" class="btn btn-small btn-danger">Überfällig</a>
        </div>
    </div>
    
    <?php if (empty($invoices)): ?>
        <p>Keine Rechnungen gefunden.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Rechnungsnr.</th>
                    <th>Kunde</th>
                    <th>Datum</th>
                    <th>Fällig am</th>
                    <th>Betrag</th>
                    <th>Status</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['company_name'] ?? $invoice['first_name'] . ' ' . $invoice['last_name']); ?></td>
                        <td><?php echo date('d.m.Y', strtotime($invoice['invoice_date'])); ?></td>
                        <td><?php echo date('d.m.Y', strtotime($invoice['due_date'])); ?></td>
                        <td><?php echo number_format($invoice['total_amount'], 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></td>
                        <td><span class="status-badge status-<?php echo $invoice['status']; ?>"><?php echo ucfirst($invoice['status']); ?></span></td>
                        <td class="action-links">
                            <?php if ($invoice['status'] !== 'paid'): ?>
                                <a href="?page=invoice_edit&id=<?php echo $invoice['id']; ?>" class="btn btn-small">Bearbeiten</a>
                                <a href="?page=payment_edit&action=new&invoice_id=<?php echo $invoice['id']; ?>" class="btn btn-small" style="background-color: #27ae60;">Zahlung erfassen</a>
                            <?php endif; ?>
                            <a href="?page=invoice_pdf&id=<?php echo $invoice['id']; ?>" class="btn btn-small btn-success" target="_blank">PDF</a>
                            <a href="?page=invoices&action=copy&id=<?php echo $invoice['id']; ?>" class="btn btn-small btn-secondary" onclick="return confirm('Möchten Sie diese Rechnung wirklich kopieren?');">Kopieren</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
