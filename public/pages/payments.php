<?php
$paymentObj = new Payment();
$invoiceObj = new Invoice();

// Alle Zahlungen abrufen
$payments = $paymentObj->getAll();
?>

<div class="card">
    <h2><?php echo __('payment_management'); ?></h2>
    <a href="?page=payment_edit&action=new" class="btn btn-success"><?php echo __('new_payment'); ?></a>
    
    <?php if (empty($payments)): ?>
        <p style="margin-top: 20px;"><?php echo __('no_payments_found'); ?></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th><?php echo __('date'); ?></th>
                    <th><?php echo __('invoice_number'); ?></th>
                    <th><?php echo __('customer'); ?></th>
                    <th><?php echo __('amount'); ?></th>
                    <th><?php echo __('payment_method'); ?></th>
                    <th><?php echo __('reference'); ?></th>
                    <th><?php echo __('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo date('d.m.Y', strtotime($payment['payment_date'])); ?></td>
                        <td><?php echo htmlspecialchars($payment['invoice_number']); ?></td>
                        <td><?php echo htmlspecialchars($payment['company_name'] ?? $payment['first_name'] . ' ' . $payment['last_name']); ?></td>
                        <td><?php echo number_format($payment['amount'], 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                        <td><?php echo htmlspecialchars($payment['reference'] ?? '-'); ?></td>
                        <td class="action-links">
                            <a href="?page=payment_edit&id=<?php echo $payment['id']; ?>" class="btn btn-small"><?php echo __('edit'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
