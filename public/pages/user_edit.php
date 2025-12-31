<?php
$userObj = new User();

$action = $_GET['action'] ?? 'edit';
$userId = $_GET['id'] ?? null;
$error = '';
$success = '';

// Benutzer löschen
if ($action === 'delete' && $userId) {
    if ($userObj->delete($userId)) {
        header('Location: ?page=users');
        exit;
    } else {
        $error = __('error_delete_user');
    }
}

// Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    
    // Validierung
    if (empty($username)) {
        $error = __('username_required');
    } elseif ($action === 'new' && empty($password)) {
        $error = __('password_required');
    } elseif (!empty($password) && $password !== $passwordConfirm) {
        $error = __('passwords_not_match');
    } elseif (!empty($password) && strlen($password) < 6) {
        $error = __('password_too_short');
    } else {
        if ($action === 'new') {
            // Neuen Benutzer erstellen
            if ($userObj->create($username, $password)) {
                header('Location: ?page=users');
                exit;
            } else {
                $error = __('username_already_exists');
            }
        } else {
            // Benutzer bearbeiten
            if ($userObj->updateUsername($userId, $username)) {
                // Passwort ändern, falls angegeben
                if (!empty($password)) {
                    $userObj->changePassword($userId, $password);
                }
                $success = __('user_updated_success');
            } else {
                $error = __('username_already_exists');
            }
        }
    }
}

// Benutzer laden (für Bearbeitung)
$user = null;
if ($action === 'edit' && $userId) {
    $user = $userObj->getById($userId);
    if (!$user) {
        header('Location: ?page=users');
        exit;
    }
}
?>

<div class="card">
    <h2><?php echo $action === 'new' ? __('new_user') : __('edit_user'); ?></h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="username"><?php echo __('username'); ?> *</label>
            <input type="text" 
                   id="username" 
                   name="username" 
                   value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" 
                   required
                   <?php echo ($user && $user['username'] === 'admin') ? 'readonly' : ''; ?>>
        </div>
        
        <div class="form-group">
            <label for="password">
                <?php echo __('password'); ?>
                <?php echo $action === 'new' ? '*' : '(' . __('leave_empty_to_keep') . ')'; ?>
            </label>
            <input type="password" 
                   id="password" 
                   name="password"
                   <?php echo $action === 'new' ? 'required' : ''; ?>>
            <small><?php echo __('min_6_characters'); ?></small>
        </div>
        
        <div class="form-group">
            <label for="password_confirm">
                <?php echo __('password_confirm'); ?>
                <?php echo $action === 'new' ? '*' : ''; ?>
            </label>
            <input type="password" 
                   id="password_confirm" 
                   name="password_confirm"
                   <?php echo $action === 'new' ? 'required' : ''; ?>>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?php echo __('save'); ?></button>
            <a href="?page=users" class="btn btn-secondary"><?php echo __('cancel'); ?></a>
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

.form-group input[type="text"],
.form-group input[type="password"],
.form-group input[type="email"],
.form-group input[type="tel"],
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-group input[readonly] {
    background-color: #f5f5f5;
    cursor: not-allowed;
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
