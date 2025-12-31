<?php
$userObj = new User();

$error = '';
$success = '';

// Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validierung
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = __('all_fields_required');
    } elseif ($newPassword !== $confirmPassword) {
        $error = __('passwords_not_match');
    } elseif (strlen($newPassword) < 6) {
        $error = __('password_too_short');
    } else {
        // Aktuelles Passwort überprüfen
        $currentUser = $userObj->getById($_SESSION['user_id']);
        $stmt = Database::getInstance()->getConnection()->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $userData = $stmt->fetch();
        
        if (!password_verify($currentPassword, $userData['password_hash'])) {
            $error = __('current_password_incorrect');
        } else {
            // Passwort ändern
            if ($userObj->changePassword($_SESSION['user_id'], $newPassword)) {
                $success = __('password_changed_success');
            } else {
                $error = __('error_changing_password');
            }
        }
    }
}
?>

<div class="card">
    <h2><?php echo __('change_password'); ?></h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="current_password"><?php echo __('current_password'); ?> *</label>
            <input type="password" id="current_password" name="current_password" required autofocus>
        </div>
        
        <div class="form-group">
            <label for="new_password"><?php echo __('new_password'); ?> *</label>
            <input type="password" id="new_password" name="new_password" required>
            <small><?php echo __('min_6_characters'); ?></small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password"><?php echo __('password_confirm'); ?> *</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?php echo __('change_password_button'); ?></button>
            <a href="?page=dashboard" class="btn btn-secondary"><?php echo __('cancel'); ?></a>
        </div>
    </form>
</div>

<style>
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input[type="password"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}

.form-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
}

.alert {
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 4px;
}

.alert-error {
    background-color: #fee;
    border: 1px solid #fcc;
    color: #c33;
}

.alert-success {
    background-color: #efe;
    border: 1px solid #cfc;
    color: #3c3;
}
</style>
