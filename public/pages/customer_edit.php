<?php
$customerObj = new Customer();
$action = $_GET['action'] ?? 'edit';
$customerId = $_GET['id'] ?? null;
$customer = null;
$message = '';

// Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'customer_number' => $_POST['customer_number'],
        'company_name' => $_POST['company_name'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address_street' => $_POST['address_street'],
        'address_city' => $_POST['address_city'],
        'address_zip' => $_POST['address_zip'],
        'address_country' => $_POST['address_country'],
        'tax_id' => $_POST['tax_id'],
        'notes' => $_POST['notes']
    ];
    
    if ($action === 'new') {
        $result = $customerObj->create($data);
        if ($result) {
            header('Location: ?page=customers');
            exit;
        } else {
            $message = '<div class="alert alert-error">Fehler beim Erstellen des Kunden.</div>';
        }
    } else {
        $result = $customerObj->update($customerId, $data);
        if ($result) {
            $message = '<div class="alert alert-success">Kunde erfolgreich aktualisiert.</div>';
            $customer = $customerObj->getById($customerId);
        } else {
            $message = '<div class="alert alert-error">Fehler beim Aktualisieren des Kunden.</div>';
        }
    }
}

// Kunde laden (bei Bearbeitung)
if ($action === 'edit' && $customerId) {
    $customer = $customerObj->getById($customerId);
    if (!$customer) {
        die('Kunde nicht gefunden');
    }
} elseif ($action === 'new') {
    $customer = [
        'customer_number' => $customerObj->generateCustomerNumber(),
        'company_name' => '',
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
        'address_street' => '',
        'address_city' => '',
        'address_zip' => '',
        'address_country' => 'Deutschland',
        'tax_id' => '',
        'notes' => ''
    ];
}
?>

<div class="card">
    <h2><?php echo $action === 'new' ? __('new_customer') : __('edit_customer'); ?></h2>
    
    <?php echo $message; ?>
    
    <form method="POST">
        <div class="form-group">
            <label><?php echo __('customer_number'); ?> *</label>
            <input type="text" name="customer_number" value="<?php echo htmlspecialchars($customer['customer_number']); ?>" required>
        </div>
        
        <div class="form-group">
            <label><?php echo __('company_name'); ?></label>
            <input type="text" name="company_name" value="<?php echo htmlspecialchars($customer['company_name']); ?>">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label><?php echo __('first_name'); ?></label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($customer['first_name']); ?>">
            </div>
            
            <div class="form-group">
                <label><?php echo __('last_name'); ?></label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($customer['last_name']); ?>">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label><?php echo __('email'); ?></label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>">
            </div>
            
            <div class="form-group">
                <label><?php echo __('phone'); ?></label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label><?php echo __('street'); ?></label>
            <input type="text" name="address_street" value="<?php echo htmlspecialchars($customer['address_street']); ?>">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
            <div class="form-group">
                <label><?php echo __('zip'); ?></label>
                <input type="text" name="address_zip" value="<?php echo htmlspecialchars($customer['address_zip']); ?>">
            </div>
            
            <div class="form-group">
                <label><?php echo __('city'); ?></label>
                <input type="text" name="address_city" value="<?php echo htmlspecialchars($customer['address_city']); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label><?php echo __('country'); ?></label>
            <input type="text" name="address_country" value="<?php echo htmlspecialchars($customer['address_country']); ?>">
        </div>
        
        <div class="form-group">
            <label><?php echo __('tax_id'); ?></label>
            <input type="text" name="tax_id" value="<?php echo htmlspecialchars($customer['tax_id']); ?>">
        </div>
        
        <div class="form-group">
            <label><?php echo __('notes'); ?></label>
            <textarea name="notes"><?php echo htmlspecialchars($customer['notes']); ?></textarea>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success"><?php echo __('save'); ?></button>
            <a href="?page=customers" class="btn"><?php echo __('cancel'); ?></a>
        </div>
    </form>
</div>
