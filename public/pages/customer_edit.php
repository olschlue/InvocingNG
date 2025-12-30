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
    <h2><?php echo $action === 'new' ? 'Neuer Kunde' : 'Kunde bearbeiten'; ?></h2>
    
    <?php echo $message; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Kundennummer *</label>
            <input type="text" name="customer_number" value="<?php echo htmlspecialchars($customer['customer_number']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Firmenname</label>
            <input type="text" name="company_name" value="<?php echo htmlspecialchars($customer['company_name']); ?>">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Vorname</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($customer['first_name']); ?>">
            </div>
            
            <div class="form-group">
                <label>Nachname</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($customer['last_name']); ?>">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>E-Mail</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>">
            </div>
            
            <div class="form-group">
                <label>Telefon</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Straße</label>
            <input type="text" name="address_street" value="<?php echo htmlspecialchars($customer['address_street']); ?>">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
            <div class="form-group">
                <label>PLZ</label>
                <input type="text" name="address_zip" value="<?php echo htmlspecialchars($customer['address_zip']); ?>">
            </div>
            
            <div class="form-group">
                <label>Stadt</label>
                <input type="text" name="address_city" value="<?php echo htmlspecialchars($customer['address_city']); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Land</label>
            <input type="text" name="address_country" value="<?php echo htmlspecialchars($customer['address_country']); ?>">
        </div>
        
        <div class="form-group">
            <label>Steuernummer</label>
            <input type="text" name="tax_id" value="<?php echo htmlspecialchars($customer['tax_id']); ?>">
        </div>
        
        <div class="form-group">
            <label>Notizen</label>
            <textarea name="notes"><?php echo htmlspecialchars($customer['notes']); ?></textarea>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">Speichern</button>
            <a href="?page=customers" class="btn">Abbrechen</a>
        </div>
    </form>
</div>
