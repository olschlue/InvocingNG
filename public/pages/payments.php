<?php
$paymentObj = new Payment();
$invoiceObj = new Invoice();

// Löschvorgang verarbeiten
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $deleteId = $_GET['id'];
    $result = $paymentObj->delete($deleteId);
    
    if ($result['success']) {
        header('Location: ?page=payments&deleted=1');
        exit;
    } else {
        $message = '<div class="alert alert-error">Fehler: ' . $result['message'] . '</div>';
    }
}

// Erfolgsmeldung anzeigen
$message = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = '<div class="alert alert-success">' . __('payment_saved') . '</div>';
}
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $message = '<div class="alert alert-success">Zahlung erfolgreich gelöscht.</div>';
}

// Alle Zahlungen abrufen
$payments = $paymentObj->getAll();
?>

<div class="card">
    <h2><?php echo __('payment_management'); ?></h2>
    
    <?php echo $message; ?>
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
                        <td class="action-links">
                            <a href="?page=payment_edit&id=<?php echo $payment['id']; ?>" class="btn btn-small"><?php echo __('edit'); ?></a>
                            <a href="?page=payments&delete=1&id=<?php echo $payment['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Zahlung wirklich löschen? Der Status der Rechnung wird automatisch angepasst.')"><?php echo __('delete'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
