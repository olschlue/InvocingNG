<?php
$paymentObj = new Payment();
$invoiceObj = new Invoice();
$action = $_GET['action'] ?? 'edit';
$paymentId = $_GET['id'] ?? null;
$payment = null;
$message = '';

// Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'invoice_id' => $_POST['invoice_id'],
        'payment_date' => $_POST['payment_date'],
        'amount' => $_POST['amount'],
        'payment_method' => $_POST['payment_method'],
        'reference' => $_POST['reference'],
        'notes' => $_POST['notes']
    ];
    
    if ($action === 'new') {
        $result = $paymentObj->create($data);
        if ($result) {
            header('Location: ?page=payments');
            exit;
        } else {
            $message = '<div class="alert alert-error">Fehler beim Erstellen der Zahlung.</div>';
        }
    } else {
        $result = $paymentObj->update($paymentId, $data);
        if ($result) {
            $message = '<div class="alert alert-success">Zahlung erfolgreich aktualisiert.</div>';
            $payment = $paymentObj->getById($paymentId);
        } else {
            $message = '<div class="alert alert-error">Fehler beim Aktualisieren der Zahlung.</div>';
        }
    }
}

// Zahlung laden
if ($action === 'edit' && $paymentId) {
    $payment = $paymentObj->getById($paymentId);
    if (!$payment) {
        die('Zahlung nicht gefunden');
    }
} elseif ($action === 'new') {
    $payment = [
        'invoice_id' => $_GET['invoice_id'] ?? '',
        'payment_date' => date('Y-m-d'),
        'amount' => '',
        'payment_method' => 'bank_transfer',
        'reference' => '',
        'notes' => ''
    ];
}

// Alle offenen Rechnungen laden
$allInvoices = $invoiceObj->getAll();
?>

<div class="card">
    <h2><?php echo $action === 'new' ? 'Neue Zahlung' : 'Zahlung bearbeiten'; ?></h2>
    
    <?php echo $message; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Rechnung *</label>
            <select name="invoice_id" required>
                <option value="">-- Rechnung auswählen --</option>
                <?php foreach ($allInvoices as $invoice): ?>
                    <option value="<?php echo $invoice['id']; ?>" <?php echo ($payment['invoice_id'] == $invoice['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($invoice['invoice_number'] . ' - ' . ($invoice['company_name'] ?: $invoice['first_name'] . ' ' . $invoice['last_name']) . ' (' . number_format($invoice['total_amount'], 2, ',', '.') . ' ' . CURRENCY_SYMBOL . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Zahlungsdatum *</label>
                <input type="date" name="payment_date" value="<?php echo $payment['payment_date']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Betrag *</label>
                <input type="number" step="0.01" name="amount" value="<?php echo $payment['amount']; ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Zahlungsmethode</label>
            <select name="payment_method">
                <option value="bank_transfer" <?php echo ($payment['payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>>Banküberweisung</option>
                <option value="cash" <?php echo ($payment['payment_method'] == 'cash') ? 'selected' : ''; ?>>Barzahlung</option>
                <option value="credit_card" <?php echo ($payment['payment_method'] == 'credit_card') ? 'selected' : ''; ?>>Kreditkarte</option>
                <option value="paypal" <?php echo ($payment['payment_method'] == 'paypal') ? 'selected' : ''; ?>>PayPal</option>
                <option value="other" <?php echo ($payment['payment_method'] == 'other') ? 'selected' : ''; ?>>Sonstiges</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Referenz / Verwendungszweck</label>
            <input type="text" name="reference" value="<?php echo htmlspecialchars($payment['reference']); ?>">
        </div>
        
        <div class="form-group">
            <label>Notizen</label>
            <textarea name="notes"><?php echo htmlspecialchars($payment['notes']); ?></textarea>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">Speichern</button>
            <a href="?page=payments" class="btn">Abbrechen</a>
        </div>
    </form>
</div>
