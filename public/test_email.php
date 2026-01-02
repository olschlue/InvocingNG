<?php
require_once '../config/config.php';

// Nur für Admins/Entwickler zugänglich machen
if (!isset($_SESSION['user_id'])) {
    die('Nicht autorisiert');
}

$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testEmail = $_POST['test_email'] ?? '';
    
    if (empty($testEmail)) {
        $error = 'Bitte E-Mail-Adresse eingeben';
    } else {
        $emailObj = new Email();
        
        $subject = 'Test-E-Mail von InvoicingNG';
        $body = '<h2>Test-E-Mail</h2>';
        $body .= '<p>Dies ist eine Test-E-Mail von Ihrem InvoicingNG System.</p>';
        $body .= '<p><strong>SMTP-Server:</strong> ' . SMTP_HOST . '</p>';
        $body .= '<p><strong>Port:</strong> ' . SMTP_PORT . '</p>';
        $body .= '<p><strong>Verschlüsselung:</strong> ' . SMTP_ENCRYPTION . '</p>';
        $body .= '<p><strong>Von:</strong> ' . SMTP_FROM . '</p>';
        $body .= '<p><strong>Zeitpunkt:</strong> ' . date('d.m.Y H:i:s') . '</p>';
        $body .= '<hr>';
        $body .= '<p style="color: green;"><strong>✓ E-Mail-Konfiguration funktioniert!</strong></p>';
        
        $success = $emailObj->send($testEmail, $subject, $body);
        
        if ($success) {
            $result = 'Test-E-Mail erfolgreich gesendet an: ' . htmlspecialchars($testEmail);
        } else {
            $error = 'Fehler beim Senden der Test-E-Mail. Bitte Fehlerprotokoll prüfen.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Mail Test - InvoicingNG</title>
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
        <h1>📧 E-Mail Konfiguration Testen</h1>
        
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
            <h3>Aktuelle SMTP-Konfiguration</h3>
            <p><strong>Server:</strong> <?php echo htmlspecialchars(SMTP_HOST); ?></p>
            <p><strong>Port:</strong> <?php echo htmlspecialchars(SMTP_PORT); ?></p>
            <p><strong>Verschlüsselung:</strong> <?php echo htmlspecialchars(SMTP_ENCRYPTION); ?></p>
            <p><strong>Benutzername:</strong> <?php echo htmlspecialchars(SMTP_USER); ?></p>
            <p><strong>Von:</strong> <?php echo htmlspecialchars(SMTP_FROM); ?> (<?php echo htmlspecialchars(SMTP_FROM_NAME); ?>)</p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="test_email">Test-E-Mail senden an:</label>
                <input type="email" id="test_email" name="test_email" required placeholder="ihre-email@beispiel.de">
            </div>
            
            <button type="submit">✉ Test-E-Mail senden</button>
        </form>
        
        <a href="../index.php?page=dashboard" class="back-link">← Zurück zum Dashboard</a>
    </div>
</body>
</html>
