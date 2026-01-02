<?php
$invoiceObj = new Invoice();
$customerObj = new Customer();
$action = $_GET['action'] ?? 'edit';
$invoiceId = $_GET['id'] ?? null;
$invoice = null;
$message = '';

// Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prüfen ob Rechnung bezahlt ist (bei bestehenden Rechnungen)
    if ($action === 'edit' && $invoiceId) {
        $currentInvoice = $invoiceObj->getById($invoiceId);
        if ($currentInvoice && $currentInvoice['status'] === 'paid') {
            $message = '<div class="alert alert-error">Bezahlte Rechnungen können nicht bearbeitet werden.</div>';
            $_SERVER['REQUEST_METHOD'] = 'GET'; // Verarbeitung stoppen
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_invoice'])) {
        // Prüfen ob Rechnung versendet ist - dann nur Status ändern
        if ($action === 'edit' && $invoiceId) {
            $currentInvoice = $invoiceObj->getById($invoiceId);
            if ($currentInvoice && in_array($currentInvoice['status'], ['sent', 'paid', 'overdue'])) {
                // Nur Status-Änderung erlauben - alle anderen Felder von aktueller Rechnung übernehmen
                $data = [
                    'invoice_number' => $currentInvoice['invoice_number'],
                    'customer_id' => $currentInvoice['customer_id'],
                    'invoice_date' => $currentInvoice['invoice_date'],
                    'service_date' => $currentInvoice['service_date'],
                    'due_date' => $currentInvoice['due_date'],
                    'status' => $_POST['status'],
                    'tax_rate' => $currentInvoice['tax_rate'],
                    'notes' => $currentInvoice['notes'],
                    'payment_terms' => $currentInvoice['payment_terms']
                ];
                $result = $invoiceObj->update($invoiceId, $data);
                if ($result) {
                    $message = '<div class="alert alert-success">Status erfolgreich aktualisiert.</div>';
                } else {
                    $message = '<div class="alert alert-error">Fehler beim Aktualisieren des Status.</div>';
                }
                // Verarbeitung beenden - nicht weiter zum normalen Update
            } else {
                // Normale Bearbeitung für nicht gesperrte Rechnungen
                $data = [
                    'invoice_number' => $_POST['invoice_number'],
                    'customer_id' => $_POST['customer_id'],
                    'invoice_date' => $_POST['invoice_date'],
                    'service_date' => $_POST['service_date'],
                    'due_date' => $_POST['due_date'],
                    'status' => $_POST['status'],
                    'tax_rate' => $_POST['tax_rate'],
                    'notes' => $_POST['notes'],
                    'payment_terms' => $_POST['payment_terms']
                ];
                
                $result = $invoiceObj->update($invoiceId, $data);
                if ($result) {
                    $message = '<div class="alert alert-success">Rechnung erfolgreich aktualisiert.</div>';
                } else {
                    $message = '<div class="alert alert-error">Fehler beim Aktualisieren der Rechnung.</div>';
                }
            }
        } else {
            // Neue Rechnung erstellen
            $data = [
                'invoice_number' => $_POST['invoice_number'],
                'customer_id' => $_POST['customer_id'],
                'invoice_date' => $_POST['invoice_date'],
                'service_date' => $_POST['service_date'],
                'due_date' => $_POST['due_date'],
                'status' => $_POST['status'],
                'tax_rate' => $_POST['tax_rate'],
                'notes' => $_POST['notes'],
                'payment_terms' => $_POST['payment_terms']
            ];
            
            $result = $invoiceObj->create($data);
            if ($result) {
                header('Location: ?page=invoice_edit&id=' . $result);
                exit;
            } else {
                $message = '<div class="alert alert-error">Fehler beim Erstellen der Rechnung.</div>';
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
        // Prüfen ob Rechnung gesperrt ist
        if ($action === 'edit' && $invoiceId) {
            $currentInvoice = $invoiceObj->getById($invoiceId);
            if ($currentInvoice && in_array($currentInvoice['status'], ['sent', 'paid', 'overdue'])) {
                $message = '<div class="alert alert-error">Positionen können nicht mehr hinzugefügt werden, da die Rechnung bereits versendet wurde.</div>';
            } else {
                $itemData = [
                    'description' => $_POST['item_description'],
                    'quantity' => $_POST['item_quantity'],
                    'unit_price' => $_POST['item_unit_price'],
                    'tax_rate' => $_POST['item_tax_rate']
                ];
                $invoiceObj->addItem($invoiceId, $itemData);
                $message = '<div class="alert alert-success">Position hinzugefügt.</div>';
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
        // Position löschen - nur wenn Rechnung noch nicht versendet ist
        if ($action === 'edit' && $invoiceId) {
            $currentInvoice = $invoiceObj->getById($invoiceId);
            if ($currentInvoice && in_array($currentInvoice['status'], ['sent', 'paid', 'overdue'])) {
                $message = '<div class="alert alert-error">Positionen können nicht gelöscht werden, da die Rechnung bereits versendet wurde.</div>';
            } else {
                $itemId = $_POST['item_id'];
                if ($invoiceObj->deleteItem($itemId)) {
                    $message = '<div class="alert alert-success">Position gelöscht.</div>';
                } else {
                    $message = '<div class="alert alert-error">Fehler beim Löschen der Position.</div>';
                }
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_item'])) {
        // Position bearbeiten - nur wenn Rechnung noch nicht versendet ist
        if ($action === 'edit' && $invoiceId) {
            $currentInvoice = $invoiceObj->getById($invoiceId);
            if ($currentInvoice && in_array($currentInvoice['status'], ['sent', 'paid', 'overdue'])) {
                $message = '<div class="alert alert-error">Positionen können nicht bearbeitet werden, da die Rechnung bereits versendet wurde.</div>';
            } else {
                $itemId = $_POST['item_id'];
                $itemData = [
                    'description' => $_POST['item_description'],
                    'quantity' => $_POST['item_quantity'],
                    'unit_price' => $_POST['item_unit_price'],
                    'tax_rate' => $_POST['item_tax_rate']
                ];
                if ($invoiceObj->updateItem($itemId, $itemData)) {
                    $message = '<div class="alert alert-success">Position aktualisiert.</div>';
                } else {
                    $message = '<div class="alert alert-error">Fehler beim Aktualisieren der Position.</div>';
                }
            }
        }
    }
}

// GET-Parameter für Löschen verarbeiten
if (isset($_GET['delete_item']) && $action === 'edit' && $invoiceId) {
    $currentInvoice = $invoiceObj->getById($invoiceId);
    if ($currentInvoice && in_array($currentInvoice['status'], ['sent', 'paid', 'overdue'])) {
        $message = '<div class="alert alert-error">Positionen können nicht gelöscht werden, da die Rechnung bereits versendet wurde.</div>';
    } else {
        $itemId = $_GET['delete_item'];
        if ($invoiceObj->deleteItem($itemId)) {
            header('Location: ?page=invoice_edit&id=' . $invoiceId);
            exit;
        } else {
            $message = '<div class="alert alert-error">Fehler beim Löschen der Position.</div>';
        }
    }
}

// Rechnung laden
if ($action === 'edit' && $invoiceId) {
    $invoice = $invoiceObj->getById($invoiceId);
    if (!$invoice) {
        die('Rechnung nicht gefunden');
    }
    
    // Prüfen ob Rechnung bezahlt ist
    $isPaid = ($invoice['status'] === 'paid');
    // Prüfen ob Rechnung versendet, bezahlt oder überfällig ist
    $isLocked = in_array($invoice['status'], ['sent', 'paid', 'overdue']);
    
    $items = $invoiceObj->getItems($invoiceId);
    $editItemId = $_GET['edit_item'] ?? null;
} elseif ($action === 'new') {
    $isPaid = false;
    $isLocked = false;
    $invoice = [
        'invoice_number' => $invoiceObj->generateInvoiceNumber(),
        'customer_id' => '',
        'invoice_date' => date('Y-m-d'),
        'service_date' => date('Y-m-d'),
        'due_date' => date('Y-m-d', strtotime('+14 days')),
        'status' => 'draft',
        'tax_rate' => 19.00,
        'notes' => '',
        'payment_terms' => 'Bitte überweisen Sie den Betrag innerhalb von 14 Tagen auf das unten angegebene Konto.'
    ];
    $items = [];
}

$customers = $customerObj->getAll();
?>

<div class="card">
    <h2><?php echo $action === 'new' ? __('new_invoice') : __('edit_invoice'); ?></h2>
    
    <?php if (isset($isPaid) && $isPaid): ?>
        <div class="alert" style="background-color: #fff3cd; border-color: #ffc107; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <strong><?php echo __('error'); ?>:</strong> <?php echo __('invoice_paid_locked'); ?>
        </div>
    <?php elseif (isset($isLocked) && $isLocked && !$isPaid): ?>
        <div class="alert" style="background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <strong><?php echo __('error'); ?>:</strong> <?php echo __('invoice_locked'); ?>
        </div>
    <?php endif; ?>
    
    <?php echo $message; ?>
    
    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label><?php echo __('invoice_number'); ?> *</label>
                <input type="text" name="invoice_number" value="<?php echo htmlspecialchars($invoice['invoice_number']); ?>" required readonly style="background-color: #f5f5f5;">
            </div>
            
            <div class="form-group">
                <label><?php echo __('customer'); ?> *</label>
                <select name="customer_id" required <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>>
                    <option value="">-- <?php echo __('select_customer'); ?> --</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>" <?php echo ($invoice['customer_id'] == $customer['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($customer['company_name'] ?: $customer['first_name'] . ' ' . $customer['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label><?php echo __('invoice_date'); ?> *</label>
                <input type="date" name="invoice_date" value="<?php echo $invoice['invoice_date']; ?>" required <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>>
            </div>
            
            <div class="form-group">
                <label><?php echo __('service_date'); ?></label>
                <input type="date" name="service_date" value="<?php echo $invoice['service_date'] ?? ''; ?>" <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>>
            </div>
            
            <div class="form-group">
                <label><?php echo __('due_date'); ?> *</label>
                <input type="date" name="due_date" value="<?php echo $invoice['due_date']; ?>" required <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label><?php echo __('status'); ?></label>
                <select name="status">
                    <option value="draft" <?php echo ($invoice['status'] == 'draft') ? 'selected' : ''; ?>><?php echo __('status_draft'); ?></option>
                    <option value="sent" <?php echo ($invoice['status'] == 'sent') ? 'selected' : ''; ?>><?php echo __('status_sent'); ?></option>
                    <option value="paid" <?php echo ($invoice['status'] == 'paid') ? 'selected' : ''; ?> disabled><?php echo __('status_paid'); ?> (<?php echo __('payment_only'); ?>)</option>
                    <option value="overdue" <?php echo ($invoice['status'] == 'overdue') ? 'selected' : ''; ?>><?php echo __('status_overdue'); ?></option>
                    <option value="cancelled" <?php echo ($invoice['status'] == 'cancelled') ? 'selected' : ''; ?>><?php echo __('status_cancelled'); ?></option>
                </select>
            </div>
            
            <div class="form-group">
                <label><?php echo __('tax_rate'); ?> (%)</label>
                <input type="number" step="0.01" name="tax_rate" value="<?php echo $invoice['tax_rate']; ?>" <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>>
            </div>
        </div>
        
        <div class="form-group">
            <label><?php echo __('payment_terms'); ?></label>
            <textarea name="payment_terms" <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>><?php echo htmlspecialchars($invoice['payment_terms']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label><?php echo __('notes'); ?></label>
            <textarea name="notes" <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>><?php echo htmlspecialchars($invoice['notes']); ?></textarea>
        </div>
        
        <div style="display: flex; gap: 10px; margin-bottom: 30px;">
            <button type="submit" name="save_invoice" class="btn btn-success"><?php echo __('save'); ?></button>
            <a href="?page=invoices" class="btn"><?php echo __('cancel'); ?></a>
            <?php if ($action === 'edit'): ?>
                <a href="?page=invoice_pdf&id=<?php echo $invoiceId; ?>" class="btn" target="_blank">PDF <?php echo __('preview'); ?></a>
                <?php if ($invoice['status'] !== 'paid'): ?>
                    <a href="?page=payment_edit&action=new&invoice_id=<?php echo $invoiceId; ?>" class="btn" style="background-color: #27ae60;"><?php echo __('record_payment'); ?></a>
                    <a href="?page=invoice_send&id=<?php echo $invoiceId; ?>" class="btn" style="background-color: #3498db; color: white;" title="<?php echo __('send_invoice'); ?>"><?php echo __('send_email'); ?></a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </form>
    
    <?php if ($action === 'edit'): ?>
        <h3><?php echo __('invoice_items'); ?></h3>
        
        <?php if (!empty($items)): ?>
            <table>
                <thead>
                    <tr>
                        <th><?php echo __('position'); ?></th>
                        <th><?php echo __('description'); ?></th>
                        <th><?php echo __('quantity'); ?></th>
                        <th><?php echo __('unit_price'); ?></th>
                        <th><?php echo __('tax_rate'); ?> %</th>
                        <th><?php echo __('total'); ?></th>
                        <?php if (!isset($isLocked) || !$isLocked): ?>
                        <th><?php echo __('actions'); ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <?php if (isset($editItemId) && $editItemId == $item['id'] && (!isset($isLocked) || !$isLocked)): ?>
                        <!-- Bearbeitungsmodus -->
                        <tr>
                            <form method="POST">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <td><?php echo $item['position']; ?></td>
                                <td><input type="text" name="item_description" value="<?php echo htmlspecialchars($item['description']); ?>" required style="width: 100%;"></td>
                                <td><input type="number" step="0.01" name="item_quantity" value="<?php echo $item['quantity']; ?>" required style="width: 80px;"></td>
                                <td><input type="number" step="0.01" name="item_unit_price" value="<?php echo $item['unit_price']; ?>" required style="width: 100px;"></td>
                                <td><input type="number" step="0.01" name="item_tax_rate" value="<?php echo $item['tax_rate']; ?>" required style="width: 60px;"></td>
                                <td><?php echo number_format($item['total'], 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></td>
                                <td>
                                    <button type="submit" name="update_item" class="btn btn-success" style="padding: 5px 10px; font-size: 12px; height: 28px; line-height: 18px;"><?php echo __('save'); ?></button>
                                    <a href="?page=invoice_edit&id=<?php echo $invoiceId; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px; height: 28px; line-height: 18px; display: inline-block; vertical-align: middle; box-sizing: border-box;"><?php echo __('cancel'); ?></a>
                                </td>
                            </form>
                        </tr>
                        <?php else: ?>
                        <!-- Anzeigemodus -->
                        <tr>
                            <td><?php echo $item['position']; ?></td>
                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                            <td><?php echo number_format($item['quantity'], 2, ',', '.'); ?></td>
                            <td><?php echo number_format($item['unit_price'], 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></td>
                            <td><?php echo number_format($item['tax_rate'], 0); ?>%</td>
                            <td><?php echo number_format($item['total'], 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></td>
                            <?php if (!isset($isLocked) || !$isLocked): ?>
                            <td>
                                <a href="?page=invoice_edit&id=<?php echo $invoiceId; ?>&edit_item=<?php echo $item['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 12px; background-color: #3498db; color: white; height: 28px; line-height: 18px; display: inline-block; vertical-align: middle; box-sizing: border-box;"><?php echo __('edit'); ?></a>
                                <a href="?page=invoice_edit&id=<?php echo $invoiceId; ?>&delete_item=<?php echo $item['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px; height: 28px; line-height: 18px; display: inline-block; vertical-align: middle; box-sizing: border-box;" onclick="return confirm('<?php echo __('confirm_delete'); ?>?');"><?php echo __('delete'); ?></a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 20px; text-align: right;">
                <strong><?php echo __('subtotal'); ?>: <?php echo number_format($invoice['subtotal'], 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></strong><br>
                <strong><?php echo __('tax_amount'); ?>: <?php echo number_format($invoice['tax_amount'], 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></strong><br>
                <strong style="font-size: 18px;"><?php echo __('total_amount'); ?>: <?php echo number_format($invoice['total_amount'], 2, ',', '.'); ?> <?php echo APP_CURRENCY_SYMBOL; ?></strong>
            </div>
        <?php endif; ?>
        
        <?php if (!isset($isLocked) || !$isLocked): ?>
        <h4 style="margin-top: 30px;"><?php echo __('add_item'); ?></h4>
        <form method="POST">
            <div style="display: grid; grid-template-columns: 3fr 1fr 1fr 1fr; gap: 10px; align-items: end;">
                <div class="form-group">
                    <label><?php echo __('description'); ?></label>
                    <input type="text" name="item_description" required>
                </div>
                <div class="form-group">
                    <label><?php echo __('quantity'); ?></label>
                    <input type="number" step="0.01" name="item_quantity" value="1" required>
                </div>
                <div class="form-group">
                    <label><?php echo __('unit_price'); ?></label>
                    <input type="number" step="0.01" name="item_unit_price" required>
                </div>
                <div class="form-group">
                    <label><?php echo __('tax_rate'); ?> %</label>
                    <input type="number" step="0.01" name="item_tax_rate" value="19" required>
                </div>
            </div>
            <button type="submit" name="add_item" class="btn btn-success"><?php echo __('add_item'); ?></button>
        </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
