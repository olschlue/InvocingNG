<?php
require_once '../config/config.php';

// Nur für Admins/Entwickler zugänglich machen
if (!isset($_SESSION['user_id'])) {
    die('Nicht autorisiert');
}

$result = null;
$error = null;
$debugOutput = '';

// Debug-Output Buffer starten
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testEmail = $_POST['test_email'] ?? '';
    
    if (empty($testEmail)) {
        $error = __('error_enter_email_address');
    } else {
        $emailObj = new Email();
        
        $subject = __('test_email_subject');
        $body = '<h2>' . __('test_email_heading') . '</h2>';
        $body .= '<p>' . __('test_email_body') . '</p>';
        $body .= '<p><strong>' . __('smtp_server') . ':</strong> ' . SMTP_HOST . '</p>';
        $body .= '<p><strong>Port:</strong> ' . SMTP_PORT . '</p>';
        $body .= '<p><strong>' . __('smtp_encryption') . ':</strong> ' . SMTP_ENCRYPTION . '</p>';
        $body .= '<p><strong>' . __('from_label') . ':</strong> ' . SMTP_FROM . '</p>';
        $body .= '<p><strong>' . __('timestamp_label') . ':</strong> ' . date('d.m.Y H:i:s') . '</p>';
        $body .= '<hr>';
        $body .= '<p style="color: green;"><strong>' . __('test_email_success_marker') . '</strong></p>';
        
        $success = $emailObj->send($testEmail, $subject, $body);
        
        // Debug-Output erfassen
        $debugOutput = ob_get_contents();
        
        if ($success) {
            $result = __('test_email_success') . ': ' . htmlspecialchars($testEmail);
        } else {
            $error = __('test_email_failure') . ': ' . htmlspecialchars($emailObj->getLastError());
        }
    }
}

// Debug-Output Buffer beenden
ob_end_clean();
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(CURRENT_LANGUAGE ?? 'de', ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('test_email_title'); ?> - InvoicingNG</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        button {
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .config-info {
            background-color: #f0f8ff;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        .config-info h3 {
            margin-top: 0;
        }
        .config-info p {
            margin: 5px 0;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>📧 <?php echo __('test_email_heading_page'); ?></h1>
        
        <?php if ($result): ?>
            <div class="alert alert-success">
                <?php echo $result; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="config-info">
            <h3><?php echo __('current_smtp_configuration'); ?></h3>
            <p><strong><?php echo __('smtp_server'); ?>:</strong> <?php echo htmlspecialchars(SMTP_HOST); ?></p>
            <p><strong><?php echo __('smtp_port_label'); ?>:</strong> <?php echo htmlspecialchars(SMTP_PORT); ?></p>
            <p><strong><?php echo __('smtp_encryption'); ?>:</strong> <?php echo htmlspecialchars(SMTP_ENCRYPTION); ?></p>
            <p><strong><?php echo __('smtp_username'); ?>:</strong> <?php echo htmlspecialchars(SMTP_USER); ?></p>
            <p><strong><?php echo __('smtp_from_email'); ?>:</strong> <?php echo htmlspecialchars(SMTP_FROM); ?> (<?php echo htmlspecialchars(SMTP_FROM_NAME); ?>)</p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="test_email"><?php echo __('send_test_email_to'); ?></label>
                <input type="email" id="test_email" name="test_email" required placeholder="name@example.com">
            </div>
            
            <button type="submit">✉ <?php echo __('send_test_email'); ?></button>
        </form>
        
        <?php if (!empty($debugOutput)): ?>
            <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; max-height: 400px; overflow-y: auto;">
                <h3 style="margin-top: 0;"><?php echo __('debug_output'); ?>:</h3>
                <pre style="margin: 0; white-space: pre-wrap; word-wrap: break-word; font-size: 12px; font-family: 'Courier New', monospace;"><?php echo htmlspecialchars($debugOutput); ?></pre>
            </div>
        <?php endif; ?>
        
        <a href="../index.php?page=dashboard" class="back-link">← <?php echo __('back_to_dashboard'); ?></a>
    </div>
</body>
</html>
