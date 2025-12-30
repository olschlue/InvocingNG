<?php
$customerObj = new Customer();
$invoiceObj = new Invoice();
$paymentObj = new Payment();

// Statistiken abrufen
$totalCustomers = count($customerObj->getAll());
$allInvoices = $invoiceObj->getAll();
$totalInvoices = count($allInvoices);
$overdueInvoices = count($invoiceObj->getOverdue());
$paidInvoices = count($invoiceObj->getAll('paid'));

// Umsatzberechnung
$totalRevenue = 0;
$openAmount = 0;
foreach ($allInvoices as $inv) {
    $totalRevenue += $inv['total_amount'];
    if ($inv['status'] != 'paid' && $inv['status'] != 'cancelled') {
        $openAmount += $inv['total_amount'];
    }
}

$paymentStats = $paymentObj->getStatistics();
$recentInvoices = array_slice($allInvoices, 0, 5);
?>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Kunden gesamt</h3>
        <div class="value"><?php echo $totalCustomers; ?></div>
    </div>
    <div class="stat-card">
        <h3>Rechnungen gesamt</h3>
        <div class="value"><?php echo $totalInvoices; ?></div>
    </div>
    <div class="stat-card">
        <h3>Bezahlte Rechnungen</h3>
        <div class="value" style="color: #27ae60;"><?php echo $paidInvoices; ?></div>
    </div>
    <div class="stat-card">
        <h3>Überfällige Rechnungen</h3>
        <div class="value" style="color: #e74c3c;"><?php echo $overdueInvoices; ?></div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Gesamtumsatz (<?php echo date('Y'); ?>)</h3>
        <div class="value"><?php echo number_format($totalRevenue, 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></div>
    </div>
    <div class="stat-card">
        <h3>Offene Beträge</h3>
        <div class="value" style="color: #e67e22;"><?php echo number_format($openAmount, 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></div>
    </div>
    <div class="stat-card">
        <h3>Erhaltene Zahlungen</h3>
        <div class="value" style="color: #27ae60;"><?php echo $paymentStats['total_payments'] ?? 0; ?></div>
    </div>
    <div class="stat-card">
        <h3>Zahlungssumme (<?php echo date('Y'); ?>)</h3>
        <div class="value"><?php echo number_format($paymentStats['total_amount'] ?? 0, 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></div>
    </div>
</div>

<div class="card">
    <h2>Aktuelle Rechnungen</h2>
    <a href="?page=invoice_edit&action=new" class="btn btn-success">Neue Rechnung</a>
    
    <?php if (empty($recentInvoices)): ?>
        <p style="margin-top: 20px;">Noch keine Rechnungen vorhanden.</p>
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
                <?php foreach ($recentInvoices as $invoice): ?>
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
                            <?php endif; ?>
                            <a href="?page=invoice_pdf&id=<?php echo $invoice['id']; ?>" class="btn btn-small btn-success" target="_blank">PDF</a>
                            <a href="?page=invoices&action=copy&id=<?php echo $invoice['id']; ?>" class="btn btn-small btn-secondary" onclick="return confirm('Möchten Sie diese Rechnung wirklich kopieren?');">Kopieren</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p style="margin-top: 20px;">
            <a href="?page=invoices">Alle Rechnungen anzeigen →</a>
        </p>
    <?php endif; ?>
</div>

<?php if ($overdueInvoices > 0): ?>
    <div class="alert alert-error">
        <strong>Achtung!</strong> Sie haben <?php echo $overdueInvoices; ?> überfällige Rechnung(en).
        <a href="?page=invoices&filter=overdue">Jetzt anzeigen</a>
    </div>
<?php endif; ?>
