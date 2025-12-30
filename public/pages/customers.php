<?php
$customerObj = new Customer();

// Alle Kunden abrufen
$customers = $customerObj->getAll();
?>

<div class="card">
    <h2><?php echo __('customer_management'); ?></h2>
    <a href="?page=customer_edit&action=new" class="btn btn-success"><?php echo __('new_customer'); ?></a>
    
    <?php if (empty($customers)): ?>
        <p style="margin-top: 20px;"><?php echo __('no_customers_found'); ?></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th><?php echo __('customer_number'); ?></th>
                    <th><?php echo __('company_name'); ?> / <?php echo __('last_name'); ?></th>
                    <th><?php echo __('email'); ?></th>
                    <th><?php echo __('phone'); ?></th>
                    <th><?php echo __('city'); ?></th>
                    <th><?php echo __('actions'); ?></th>
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
                            <a href="?page=customer_edit&id=<?php echo $customer['id']; ?>" class="btn btn-small"><?php echo __('edit'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
