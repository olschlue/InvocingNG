<?php
$paymentObj = new Payment();
$invoiceObj = new Invoice();

// Alle Zahlungen abrufen
$payments = $paymentObj->getAll();
?>

<div class="card">
    <h2>Zahlungsverwaltung</h2>
    <a href="?page=payment_edit&action=new" class="btn btn-success">Neue Zahlung</a>
    
    <?php if (empty($payments)): ?>
        <p style="margin-top: 20px;">Noch keine Zahlungen vorhanden.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Rechnungsnr.</th>
                    <th>Kunde</th>
                    <th>Betrag</th>
                    <th>Zahlungsmethode</th>
                    <th>Referenz</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo date('d.m.Y', strtotime($payment['payment_date'])); ?></td>
                        <td><?php echo htmlspecialchars($payment['invoice_number']); ?></td>
                        <td><?php echo htmlspecialchars($payment['company_name'] ?? $payment['first_name'] . ' ' . $payment['last_name']); ?></td>
                        <td><?php echo number_format($payment['amount'], 2, ',', '.'); ?> <?php echo CURRENCY_SYMBOL; ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                        <td><?php echo htmlspecialchars($payment['reference'] ?? '-'); ?></td>
                        <td class="action-links">
                            <a href="?page=payment_edit&id=<?php echo $payment['id']; ?>" class="btn btn-small">Bearbeiten</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
