<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Rechnungsverwaltung</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: <?php echo APP_BACKGROUND_COLOR; ?>;
            color: <?php echo APP_TEXT_COLOR; ?>;
            line-height: 1.6;
            padding-top: 120px; /* Platz für sticky navbar */
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: <?php echo APP_BACKGROUND_COLOR; ?>;
            color: <?php echo APP_TEXT_COLOR; ?>;
            margin-bottom: 5px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            /*box-shadow: 0 2px 4px rgba(0,0,0,0.1);*/
        }
        
        header .header-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        header .logo {
            width: auto;
        }
        
        header h1 {
            margin: 0;
            color: <?php echo APP_PRIMARY_COLOR; ?>;
        }
        
        nav {
            background: <?php echo APP_SECONDARY_COLOR; ?>;
            padding: 10px 0;
            margin-bottom: 30px;
            position: fixed;
            top: 60px; /* Nach dem Header */
            left: 0;
            right: 0;
            z-index: 999;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        nav .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }
        
        nav a {
            color: <?php echo APP_TEXT_COLOR; ?>;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        nav a:hover, nav a.active {
            background: <?php echo APP_PRIMARY_COLOR; ?>;
            color: white;
        }
        
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            color: <?php echo APP_TEXT_COLOR; ?>;
        }
        
        .card h2 {
            margin-bottom: 20px;
            color: <?php echo APP_PRIMARY_COLOR; ?>;
            border-bottom: 2px solid <?php echo APP_ACCENT_COLOR; ?>;
            padding-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            color: <?php echo APP_TEXT_COLOR; ?>;
        }
        
        th {
            background: <?php echo APP_SECONDARY_COLOR; ?>;
            color: #333;
            font-weight: 600;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: <?php echo APP_ACCENT_COLOR; ?>;
            color: <?php echo APP_TEXT_COLOR; ?>;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: <?php echo APP_PRIMARY_COLOR; ?>;
            color: white;
        }
        
        .btn-primary {
            background: <?php echo APP_PRIMARY_COLOR; ?>;
            color: white;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
        }
        
        .btn-secondary {
            background: <?php echo APP_ACCENT_COLOR; ?>;
            color: <?php echo APP_TEXT_COLOR; ?>;
        }
        
        .btn-secondary:hover {
            background: <?php echo APP_PRIMARY_COLOR; ?>;
            color: white;
        }
        
        .btn-success {
            background: <?php echo APP_SUCCESS_COLOR; ?>;
            color: white;
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .btn-danger {
            background: <?php echo APP_DANGER_COLOR; ?>;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-draft {
            background: #95a5a6;
            color: white;
        }
        
        .status-sent {
            background: #3498db;
            color: white;
        }
        
        .status-paid {
            background: #27ae60;
            color: white;
        }
        
        .status-overdue {
            background: #e74c3c;
            color: white;
        }
        
        .status-cancelled {
            background: #7f8c8d;
            color: white;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            color: <?php echo APP_TEXT_COLOR; ?>;
        }
        
        .stat-card h3 {
            color: <?php echo APP_TEXT_COLOR; ?>;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: <?php echo APP_TEXT_COLOR; ?>;
        }
        
        .action-links {
            display: flex;
            gap: 10px;
        }
        
        footer {
            margin-top: 50px;
            padding: 20px 0;
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <?php if (defined('APP_LOGO') && file_exists(APP_LOGO)): ?>
                    <img src="assets/logo.png" alt="<?php echo APP_NAME; ?>" class="logo">
                <?php endif; ?>
                <h1><?php echo APP_NAME; ?></h1>
            </div>
        </div>
    </header>
    <nav>
        <div class="container">
            <ul>
                <li><a href="index.php" <?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'class="active"' : ''; ?>><?php echo __('dashboard'); ?></a></li>
                <li><a href="?page=customers" <?php echo (isset($_GET['page']) && $_GET['page'] == 'customers') ? 'class="active"' : ''; ?>><?php echo __('customers'); ?></a></li>
                <li><a href="?page=invoices" <?php echo (isset($_GET['page']) && $_GET['page'] == 'invoices') ? 'class="active"' : ''; ?>><?php echo __('invoices'); ?></a></li>
                <li><a href="?page=payments" <?php echo (isset($_GET['page']) && $_GET['page'] == 'payments') ? 'class="active"' : ''; ?>><?php echo __('payments'); ?></a></li>
                <li style="margin-left: auto;"><a href="?page=company_settings" <?php echo (isset($_GET['page']) && $_GET['page'] == 'company_settings') ? 'class="active"' : ''; ?>><?php echo __('company_settings'); ?></a></li>
                <li><a href="?page=users" <?php echo (isset($_GET['page']) && $_GET['page'] == 'users') ? 'class="active"' : ''; ?>><?php echo __('users'); ?></a></li>                
                <li><a href="?logout=1" onclick="return confirm('<?php echo __('confirm_logout'); ?>');"><?php echo __('logout'); ?></a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
