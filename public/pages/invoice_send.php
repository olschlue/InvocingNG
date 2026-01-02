<?php
$invoiceId = $_GET['id'] ?? null;
$message = '';
$error = '';

if (!$invoiceId) {
    header('Location: ?page=invoices');
    exit;
}

$invoiceObj = new Invoice();
$invoice = $invoiceObj->getById($invoiceId);

if (!$invoice) {
    header('Location: ?page=invoices');
    exit;
}

// E-Mail senden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipientEmail = $_POST['email'] ?? $invoice['email'];
    $customMessage = $_POST['message'] ?? '';
    
    $emailObj = new Email();
    $result = $emailObj->sendInvoice($invoiceId, $recipientEmail, $customMessage);
    
    if ($result['success']) {
        $_SESSION['success_message'] = $result['message'];
        header('Location: ?page=invoices');
        exit;
    } else {
        $error = $result['message'];
    }
}

$customerName = $invoice['company_name'] ?: ($invoice['first_name'] . ' ' . $invoice['last_name']);
?>

<div class="card">
    <h2><?php echo __('send_invoice'); ?>: <?php echo htmlspecialchars($invoice['invoice_number']); ?></h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error" style="background-color: #fee; border: 1px solid #fcc; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-grid">
            <div class="form-group">
                <label><?php echo __('customer'); ?></label>
                <input type="text" value="<?php echo htmlspecialchars($customerName); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label><?php echo __('invoice_number'); ?></label>
                <input type="text" value="<?php echo htmlspecialchars($invoice['invoice_number']); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label><?php echo __('invoice_date'); ?></label>
                <input type="text" value="<?php echo date('d.m.Y', strtotime($invoice['invoice_date'])); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label><?php echo __('total_amount'); ?></label>
                <input type="text" value="<?php echo number_format($invoice['total_amount'], 2, ',', '.') . ' ' . CURRENCY; ?>" disabled>
            </div>
        </div>
        
        <div class="form-group">
            <label for="email"><?php echo __('recipient_email'); ?> *</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($invoice['email']); ?>" required>
            <small><?php echo __('email_hint'); ?></small>
        </div>
        
        <div class="form-group">
            <label for="message"><?php echo __('custom_message'); ?></label>
            <textarea id="message" name="message" rows="8" placeholder="<?php echo __('custom_message_placeholder'); ?>"></textarea>
            <small><?php echo __('custom_message_hint'); ?></small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <span style="margin-right: 5px;">✉</span> <?php echo __('send_email'); ?>
            </button>
            <a href="?page=invoices" class="btn btn-secondary"><?php echo __('cancel'); ?></a>
            <a href="?page=invoice_pdf&id=<?php echo $invoiceId; ?>" class="btn" target="_blank" style="margin-left: auto;">
                <span style="margin-right: 5px;">👁</span> <?php echo __('preview_pdf'); ?>
            </a>
        </div>
    </form>
    
    <div style="margin-top: 30px; padding: 15px; background-color: #f0f8ff; border: 1px solid #cce; border-radius: 4px;">
        <h3 style="margin-top: 0;"><?php echo __('email_preview'); ?></h3>
        <p><strong><?php echo __('subject'); ?>:</strong> <?php echo __('email_invoice_subject') . ' ' . htmlspecialchars($invoice['invoice_number']); ?></p>
        <hr>
        <p><?php echo __('email_greeting'); ?> <?php echo htmlspecialchars($customerName); ?>,</p>
        <p><?php echo __('email_invoice_body'); ?></p>
        <p><?php echo __('email_invoice_details'); ?>:<br>
        <?php echo __('invoice_number'); ?>: <?php echo htmlspecialchars($invoice['invoice_number']); ?><br>
        <?php echo __('invoice_date'); ?>: <?php echo date('d.m.Y', strtotime($invoice['invoice_date'])); ?><br>
        <?php echo __('total_amount'); ?>: <?php echo number_format($invoice['total_amount'], 2, ',', '.') . ' ' . CURRENCY; ?><br>
        <?php echo __('due_date'); ?>: <?php echo date('d.m.Y', strtotime($invoice['due_date'])); ?></p>
        <p><?php echo __('email_invoice_attached'); ?></p>
        <p><?php echo __('email_regards'); ?><br>
        <?php echo SMTP_FROM_NAME; ?></p>
    </div>
</div>

<style>
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-group input:disabled {
    background-color: #f5f5f5;
    color: #666;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}

.alert-error {
    color: #c00;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        margin-left: 0 !important;
    }
}
</style>
