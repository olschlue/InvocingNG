<?php
$settingsObj = new Settings();
$db = Database::getInstance()->getConnection();
$message = '';
$error = '';

// Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // SMTP und App-Einstellungen in settings Tabelle speichern
    $settings = [
        'company_name' => $_POST['company_name'] ?? '',
        'app_name' => $_POST['app_name'] ?? '',
        'smtp_host' => $_POST['smtp_host'] ?? '',
        'smtp_port' => $_POST['smtp_port'] ?? '',
        'smtp_user' => $_POST['smtp_user'] ?? '',
        'smtp_from' => $_POST['smtp_from'] ?? '',
        'smtp_from_name' => $_POST['smtp_from_name'] ?? '',
        'smtp_encryption' => $_POST['smtp_encryption'] ?? 'ssl'
    ];
    
    // Passwort nur aktualisieren, wenn eingegeben
    if (!empty($_POST['smtp_pass'])) {
        $settings['smtp_pass'] = $_POST['smtp_pass'];
    }
    
    $success = true;
    
    // Settings speichern
    if (!$settingsObj->setMultiple($settings)) {
        $success = false;
    }
    
    // VAT-ID in company_settings Tabelle speichern
    try {
        $vat_id = $_POST['company_vat_id'] ?? '';
        
        // Prüfen ob company_settings Eintrag existiert
        $stmt = $db->query("SELECT COUNT(*) FROM company_settings");
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            // Update
            $stmt = $db->prepare("UPDATE company_settings SET vat_id = ? LIMIT 1");
            $stmt->execute([$vat_id]);
        } else {
            // Insert - ersten Eintrag erstellen
            $stmt = $db->prepare("INSERT INTO company_settings (company_name, vat_id) VALUES (?, ?)");
            $stmt->execute([$_POST['company_name'] ?? 'Meine Firma', $vat_id]);
        }
    } catch (PDOException $e) {
        $success = false;
        error_log("Error saving VAT-ID: " . $e->getMessage());
    }
    
    if ($success) {
        $message = __('settings_saved');
        // Cache leeren damit neue Werte geladen werden
        Settings::clearCache();
    } else {
        $error = __('settings_error');
    }
}

// Aktuelle Einstellungen laden
$currentSettings = $settingsObj->getAll();

// VAT-ID aus company_settings Tabelle laden
$company_vat_id = '';
try {
    $stmt = $db->query("SELECT vat_id FROM company_settings LIMIT 1");
    $result = $stmt->fetch();
    if ($result) {
        $company_vat_id = $result['vat_id'] ?? '';
    }
} catch (PDOException $e) {
    error_log("Error loading VAT-ID: " . $e->getMessage());
}
?>

<div class="card">
    <h2><?php echo __('company_settings'); ?></h2>
    
    <?php if ($message): ?>
        <div class="alert alert-success" style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error" style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <h3><?php echo __('company_information'); ?></h3>
        
        <div class="form-group">
            <label for="company_name"><?php echo __('company_name_label'); ?> *</label>
            <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($currentSettings['company_name'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="app_name"><?php echo __('app_name_label'); ?> *</label>
            <input type="text" id="app_name" name="app_name" value="<?php echo htmlspecialchars($currentSettings['app_name'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="company_vat_id"><?php echo __('vat_id'); ?></label>
            <input type="text" id="company_vat_id" name="company_vat_id" value="<?php echo htmlspecialchars($company_vat_id); ?>" placeholder="DE123456789">           
        </div>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        
        <h2><?php echo __('smtp_settings'); ?></h2>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="smtp_host"><?php echo __('smtp_server'); ?> *</label>
                <input type="text" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($currentSettings['smtp_host'] ?? ''); ?>" required placeholder="smtp.beispiel.de">
            </div>
            
            <div class="form-group">
                <label for="smtp_port"><?php echo __('smtp_port_label'); ?> *</label>
                <input type="number" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars($currentSettings['smtp_port'] ?? '465'); ?>" required>
                <small><?php echo __('smtp_port_hint'); ?></small>
            </div>
        </div>
        
        <div class="form-group">
            <label for="smtp_encryption"><?php echo __('smtp_encryption'); ?> *</label>
            <select id="smtp_encryption" name="smtp_encryption" required>
                <option value="ssl" <?php echo ($currentSettings['smtp_encryption'] ?? 'ssl') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                <option value="tls" <?php echo ($currentSettings['smtp_encryption'] ?? 'ssl') === 'tls' ? 'selected' : ''; ?>>TLS</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="smtp_user"><?php echo __('smtp_username'); ?> *</label>
            <input type="text" id="smtp_user" name="smtp_user" value="<?php echo htmlspecialchars($currentSettings['smtp_user'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="smtp_pass"><?php echo __('smtp_password'); ?></label>
            <input type="password" id="smtp_pass" name="smtp_pass" placeholder="<?php echo __('smtp_password_hint'); ?>">
            <small><?php echo __('smtp_password_security'); ?></small>
        </div>
        
        <div class="form-group">
            <label for="smtp_from"><?php echo __('smtp_from_email'); ?> *</label>
            <input type="email" id="smtp_from" name="smtp_from" value="<?php echo htmlspecialchars($currentSettings['smtp_from'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="smtp_from_name"><?php echo __('smtp_from_name'); ?> *</label>
            <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?php echo htmlspecialchars($currentSettings['smtp_from_name'] ?? ''); ?>" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-success"><?php echo __('save'); ?></button>
            <a href="?page=dashboard" class="btn btn-secondary"><?php echo __('cancel'); ?></a>
            <a href="test_email.php" class="btn" style="background-color: #3498db; color: white; margin-left: auto;"><?php echo __('test_email'); ?></a>
        </div>
    </form>
</div>

<style>
.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}
</style>
