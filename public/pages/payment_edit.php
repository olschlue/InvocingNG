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
        'reference' => $_POST['reference'] ?? '',
        'notes' => $_POST['notes'] ?? ''
    ];
    
    if ($action === 'new') {
        try {
            $result = $paymentObj->create($data);
            if ($result) {
                // Erfolgreicher Redirect
                header('Location: ?page=payments&success=1');
                exit;
            } else {
                $message = '<div class="alert alert-error">' . __('error') . ': ' . __('error_payment_create') . '</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="alert alert-error">' . __('error') . ': ' . $e->getMessage() . '</div>';
        }
    } else {
        try {
            $result = $paymentObj->update($paymentId, $data);
            if ($result) {
                $message = '<div class="alert alert-success">' . __('payment_saved') . '</div>';
                $payment = $paymentObj->getById($paymentId);
            } else {
                $message = '<div class="alert alert-error">' . __('error') . ': ' . __('error_payment_update') . '</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="alert alert-error">' . __('error') . ': ' . $e->getMessage() . '</div>';
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
    $invoiceIdFromUrl = $_GET['invoice_id'] ?? '';
    $prefilledAmount = '';
    $prefilledReference = '';
    
    // Wenn eine Rechnung aus der URL übergeben wurde, lade deren Betrag und Daten
    if ($invoiceIdFromUrl) {
        $invoice = $invoiceObj->getById($invoiceIdFromUrl);
        if ($invoice) {
            // Berechne bereits bezahlten Betrag
            $totalPaid = $paymentObj->getTotalPaidForInvoice($invoiceIdFromUrl);
            // Verbleibender Betrag
            $prefilledAmount = $invoice['total_amount'] - $totalPaid;
            
            // Zweck vorbelegen mit Rechnungsnummer und Kunde
            $customerName = !empty($invoice['company_name']) 
                ? $invoice['company_name'] 
                : $invoice['first_name'] . ' ' . $invoice['last_name'];
            $prefilledReference = 'Rechnung ' . $invoice['invoice_number'] . ' - ' . $customerName;
        }
    }
    
    $payment = [
        'invoice_id' => $invoiceIdFromUrl,
        'payment_date' => date('Y-m-d'),
        'amount' => $prefilledAmount,
        'payment_method' => 'bank_transfer',
        'reference' => $prefilledReference,
        'notes' => ''
    ];
}

// Alle offenen Rechnungen laden (nur sent und overdue, nicht draft und paid)
$allInvoices = $invoiceObj->getAll();
// Filtere nur Rechnungen die für Zahlungen relevant sind
// Beim Bearbeiten muss die zugehörige Rechnung auch angezeigt werden, selbst wenn sie bezahlt ist
$currentInvoiceId = $payment['invoice_id'] ?? null;
$allInvoices = array_filter($allInvoices, function($inv) use ($currentInvoiceId) {
    // Bei neuer Zahlung: nur offene Rechnungen
    // Beim Bearbeiten: auch die aktuelle Rechnung einschließen
    return in_array($inv['status'], ['sent', 'overdue']) || ($inv['id'] == $currentInvoiceId);
});

// Rechnungsdaten für JavaScript vorbereiten
$invoiceData = [];
foreach ($allInvoices as $inv) {
    $totalPaid = $paymentObj->getTotalPaidForInvoice($inv['id']);
    $customerName = !empty($inv['company_name']) 
        ? $inv['company_name'] 
        : $inv['first_name'] . ' ' . $inv['last_name'];
    
    $invoiceData[$inv['id']] = [
        'total_amount' => $inv['total_amount'],
        'total_paid' => $totalPaid,
        'remaining' => $inv['total_amount'] - $totalPaid,
        'invoice_number' => $inv['invoice_number'],
        'customer_name' => $customerName
    ];
}
?>

<div class="card">
    <h2><?php echo $action === 'new' ? __('new_payment') : __('edit_payment'); ?></h2>
    
    <?php echo $message; ?>
    
    <form method="POST">
        <div class="form-group">
            <label><?php echo __('invoice'); ?> *</label>
            <select name="invoice_id" id="invoice_select" required>
                <option value="">-- <?php echo __('select_invoice'); ?> --</option>
                <?php foreach ($allInvoices as $invoice): ?>
                    <option value="<?php echo $invoice['id']; ?>" <?php echo (isset($payment['invoice_id']) && $payment['invoice_id'] == $invoice['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($invoice['invoice_number'] . ' - ' . ($invoice['company_name'] ?: $invoice['first_name'] . ' ' . $invoice['last_name']) . ' (' . number_format($invoice['total_amount'], 2, ',', '.') . ' ' . APP_CURRENCY_SYMBOL . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label><?php echo __('payment_date'); ?> *</label>
                <input type="date" name="payment_date" value="<?php echo $payment['payment_date']; ?>" required>
            </div>
            
            <div class="form-group">
                <label><?php echo __('amount'); ?> *</label>
                <input type="number" step="0.01" name="amount" value="<?php echo $payment['amount']; ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label><?php echo __('payment_method'); ?> *</label>
            <select name="payment_method" required>
                <option value="cash" <?php echo ($payment['payment_method'] == 'cash') ? 'selected' : ''; ?>><?php echo __('cash'); ?></option>
                <option value="bank_transfer" <?php echo ($payment['payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>><?php echo __('bank_transfer'); ?></option>
                <option value="credit_card" <?php echo ($payment['payment_method'] == 'credit_card') ? 'selected' : ''; ?>><?php echo __('credit_card'); ?></option>
                <option value="paypal" <?php echo ($payment['payment_method'] == 'paypal') ? 'selected' : ''; ?>><?php echo __('paypal'); ?></option>
            </select>
        </div>
        
        <div class="form-group">
            <label><?php echo __('reference'); ?></label>
            <input type="text" name="reference" id="reference" value="<?php echo htmlspecialchars($payment['reference']); ?>">
        </div>
        
        <div class="form-group">
            <label><?php echo __('notes'); ?></label>
            <textarea name="notes"><?php echo htmlspecialchars($payment['notes']); ?></textarea>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success"><?php echo __('save'); ?></button>
            <a href="?page=payments" class="btn"><?php echo __('cancel'); ?></a>
        </div>
    </form>
</div>

<script>
// Rechnungsdaten als JSON
const invoiceData = <?php echo json_encode($invoiceData); ?>;

// Event Listener für Rechnungsauswahl
document.getElementById('invoice_select').addEventListener('change', function() {
    const invoiceId = this.value;
    const amountField = document.querySelector('input[name="amount"]');
    const referenceField = document.getElementById('reference');
    
    if (invoiceId && invoiceData[invoiceId]) {
        const data = invoiceData[invoiceId];
        
        // Betrag vorbelegen mit verbleibendem Betrag
        amountField.value = data.remaining.toFixed(2);
        
        // Referenz vorbelegen
        referenceField.value = 'Rechnung ' + data.invoice_number + ' - ' + data.customer_name;
    } else {
        // Felder leeren wenn keine Rechnung ausgewählt
        amountField.value = '';
        referenceField.value = '';
    }
});
</script>
