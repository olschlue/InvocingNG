<?php
$customerObj = new Customer();

// Alle Kunden abrufen
$customers = $customerObj->getAll();
?>

<div class="card">
    <h2>Kundenverwaltung</h2>
    <a href="?page=customer_edit&action=new" class="btn btn-success">Neuer Kunde</a>
    
    <?php if (empty($customers)): ?>
        <p style="margin-top: 20px;">Noch keine Kunden vorhanden.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Kundennr.</th>
                    <th>Firma / Name</th>
                    <th>E-Mail</th>
                    <th>Telefon</th>
                    <th>Stadt</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer['customer_number']); ?></td>
                        <td>
                            <?php 
                            if (!empty($customer['company_name'])) {
                                echo htmlspecialchars($customer['company_name']);
                            } else {
                                echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']);
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($customer['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($customer['phone'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($customer['address_city'] ?? '-'); ?></td>
                        <td class="action-links">
                            <a href="?page=customer_edit&id=<?php echo $customer['id']; ?>" class="btn btn-small">Bearbeiten</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
