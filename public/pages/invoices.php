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
    <h2><?php echo __('invoice_management'); ?></h2>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?php 
            echo htmlspecialchars($_SESSION['success_message']); 
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="?page=invoice_edit&action=new" class="btn btn-success"><?php echo __('new_invoice'); ?></a>
        
        <div style="display: flex; gap: 10px;">
            <a href="?page=invoices&filter=all" class="btn btn-small <?php echo $filter === 'all' ? '' : 'btn-secondary'; ?>"><?php echo __('all'); ?></a>
            <a href="?page=invoices&filter=draft" class="btn btn-small"><?php echo __('draft'); ?></a>
            <a href="?page=invoices&filter=sent" class="btn btn-small"><?php echo __('sent'); ?></a>
            <a href="?page=invoices&filter=paid" class="btn btn-small"><?php echo __('paid'); ?></a>
            <a href="?page=invoices&filter=overdue" class="btn btn-small btn-danger"><?php echo __('overdue'); ?></a>
        </div>
    </div>
    
    <?php if (empty($invoices)): ?>
        <p><?php echo __('no_invoices_found'); ?></p>
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
                <?php foreach ($invoices as $invoice): ?>
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
                                <!-- <a href="?page=payment_edit&action=new&invoice_id=<?php echo $invoice['id']; ?>" class="btn btn-small" style="background-color: #27ae60;"><?php echo __('record_payment'); ?></a> -->
                            <?php endif; ?>
                            <a href="?page=invoice_pdf&id=<?php echo $invoice['id']; ?>" class="btn btn-small btn-success" target="_blank">PDF</a>
                            <a href="?page=invoices&action=copy&id=<?php echo $invoice['id']; ?>" class="btn btn-small btn-secondary" onclick="return confirm('<?php echo __('copy_invoice_confirm'); ?>')"><?php echo __('copy'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
