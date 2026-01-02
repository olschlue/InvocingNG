<?php
$settingsObj = new Settings();
$message = '';
$error = '';

// Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'company_name' => $_POST['company_name'] ?? '',
        'app_name' => $_POST['app_name'] ?? '',
        'company_vat_id' => $_POST['company_vat_id'] ?? '',
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
    
    if ($settingsObj->setMultiple($settings)) {
        $message = 'Einstellungen erfolgreich gespeichert.';
        // Cache leeren damit neue Werte geladen werden
        Settings::clearCache();
    } else {
        $error = 'Fehler beim Speichern der Einstellungen.';
    }
}

// Aktuelle Einstellungen laden
$currentSettings = $settingsObj->getAll();
?>

<div class="card">
    <h2>🏢 <?php echo __('company_settings'); ?></h2>
    
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
        <h3>Firmeninformationen</h3>
        
        <div class="form-group">
            <label for="company_name">Firmenname *</label>
            <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($currentSettings['company_name'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="app_name">Anwendungsname *</label>
            <input type="text" id="app_name" name="app_name" value="<?php echo htmlspecialchars($currentSettings['app_name'] ?? ''); ?>" required>
            <small>Dieser Name wird in der Anwendung angezeigt</small>
        </div>
        
        <div class="form-group">
            <label for="company_vat_id">Umsatzsteuer-ID (USt-IdNr.)</label>
            <input type="text" id="company_vat_id" name="company_vat_id" value="<?php echo htmlspecialchars($currentSettings['company_vat_id'] ?? ''); ?>" placeholder="DE123456789">
            <small>Die USt-IdNr. Ihrer Firma</small>
        </div>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        
        <h3>E-Mail-Server (SMTP) Einstellungen</h3>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="smtp_host">SMTP Server *</label>
                <input type="text" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($currentSettings['smtp_host'] ?? ''); ?>" required placeholder="smtp.beispiel.de">
            </div>
            
            <div class="form-group">
                <label for="smtp_port">SMTP Port *</label>
                <input type="number" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars($currentSettings['smtp_port'] ?? '465'); ?>" required>
                <small>465 für SSL, 587 für TLS</small>
            </div>
        </div>
        
        <div class="form-group">
            <label for="smtp_encryption">Verschlüsselung *</label>
            <select id="smtp_encryption" name="smtp_encryption" required>
                <option value="ssl" <?php echo ($currentSettings['smtp_encryption'] ?? 'ssl') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                <option value="tls" <?php echo ($currentSettings['smtp_encryption'] ?? 'ssl') === 'tls' ? 'selected' : ''; ?>>TLS</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="smtp_user">SMTP Benutzername *</label>
            <input type="text" id="smtp_user" name="smtp_user" value="<?php echo htmlspecialchars($currentSettings['smtp_user'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="smtp_pass">SMTP Passwort</label>
            <input type="password" id="smtp_pass" name="smtp_pass" placeholder="Leer lassen, um beizubehalten">
            <small>Aus Sicherheitsgründen wird das aktuelle Passwort nicht angezeigt</small>
        </div>
        
        <div class="form-group">
            <label for="smtp_from">Absender E-Mail *</label>
            <input type="email" id="smtp_from" name="smtp_from" value="<?php echo htmlspecialchars($currentSettings['smtp_from'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="smtp_from_name">Absender Name *</label>
            <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?php echo htmlspecialchars($currentSettings['smtp_from_name'] ?? ''); ?>" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-success">💾 <?php echo __('save'); ?></button>
            <a href="?page=dashboard" class="btn btn-secondary"><?php echo __('cancel'); ?></a>
            <a href="test_email.php" class="btn" style="background-color: #3498db; color: white; margin-left: auto;">✉ E-Mail testen</a>
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
