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

// Umsatzberechnung für aktuelles Jahr und Vorjahr
$currentYear = date('Y');
$previousYear = $currentYear - 1;
$totalRevenue = 0;
$previousYearRevenue = 0;
$openAmount = 0;
foreach ($allInvoices as $inv) {
    $invoiceYear = date('Y', strtotime($inv['invoice_date']));
    // Nur Rechnungen des aktuellen Jahres für Gesamtumsatz
    if ($invoiceYear == $currentYear) {
        $totalRevenue += $inv['total_amount'];
    }
    // Vorjahres-Umsatz
    if ($invoiceYear == $previousYear) {
        $previousYearRevenue += $inv['total_amount'];
    }
    // Offene Beträge (alle Jahre)
    if ($inv['status'] != 'paid' && $inv['status'] != 'cancelled') {
        $openAmount += $inv['total_amount'];
    }
}

$paymentStats = $paymentObj->getStatistics();
$recentInvoices = array_slice($allInvoices, 0, 5);
?>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo __('total_customers'); ?></h3>
        <div class="value"><?php echo $totalCustomers; ?></div>
    </div>
    <div class="stat-card">
        <h3><?php echo __('total_invoices'); ?></h3>
        <div class="value"><?php echo $totalInvoices; ?></div>
    </div>
    <div class="stat-card">
        <h3><?php echo __('paid_invoices'); ?></h3>
        <div class="value" style="color: #27ae60;"><?php echo $paidInvoices; ?></div>
    </div>
    <div class="stat-card">
        <h3><?php echo __('overdue_invoices'); ?></h3>
        <div class="value" style="color: #e74c3c;"><?php echo $overdueInvoices; ?></div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo __('total_revenue'); ?> (<?php echo date('Y'); ?>)</h3>
        <div class="value"><?php echo number_format($totalRevenue, 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></div>
    </div>
    <div class="stat-card">
        <h3><?php echo __('open_amount'); ?></h3>
        <div class="value" style="color: #e67e22;"><?php echo number_format($openAmount, 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></div>
    </div>
    <div class="stat-card">
        <h3><?php echo __('recent_payments'); ?></h3>
        <div class="value" style="color: #27ae60;"><?php echo $paymentStats['total_payments'] ?? 0; ?></div>
    </div>
    <div class="stat-card">
        <h3><?php echo __('total_revenue'); ?> (<?php echo $previousYear; ?>)</h3>
        <div class="value"><?php echo number_format($previousYearRevenue, 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></div>
    </div>
</div>

<div class="card">
    <h2><?php echo __('recent_invoices'); ?></h2>
    <a href="?page=invoice_edit&action=new" class="btn btn-success"><?php echo __('new_invoice'); ?></a>
    
    <?php if (empty($recentInvoices)): ?>
        <p style="margin-top: 20px;"><?php echo __('no_invoices_yet'); ?></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th><?php echo __('invoice_number'); ?></th>
                    <th><?php echo __('customer'); ?></th>
                    <th><?php echo __('date'); ?></th>
                    <th><?php echo __('due_on'); ?></th>
                    <th><?php echo __('amount'); ?></th>
                    <th><?php echo __('status'); ?></th>
                    <th><?php echo __('actions'); ?></th>
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
                        <td><span class="status-badge status-<?php echo $invoice['status']; ?>"><?php echo __('status_' . $invoice['status']); ?></span></td>
                        <td class="action-links">
                            <?php if ($invoice['status'] === 'paid'): ?>
                                <a href="?page=invoice_edit&id=<?php echo $invoice['id']; ?>" class="btn btn-small"><?php echo __('view'); ?></a>
                            <?php else: ?>
                                <a href="?page=invoice_edit&id=<?php echo $invoice['id']; ?>" class="btn btn-small"><?php echo __('edit'); ?></a>
                            <?php endif; ?>
                            <a href="?page=invoice_pdf&id=<?php echo $invoice['id']; ?>" class="btn btn-small btn-success" target="_blank">PDF</a>
                            <a href="?page=invoices&action=copy&id=<?php echo $invoice['id']; ?>" class="btn btn-small btn-secondary" onclick="return confirm('<?php echo __('copy_invoice_confirm'); ?>')"><?php echo __('copy'); ?></a>
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
