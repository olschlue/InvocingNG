<?php
$userObj = new User();

// Alle Benutzer abrufen
$users = $userObj->getAll();
?>

<div class="card">
    <h2><?php echo __('user_management'); ?></h2>
    <a href="?page=user_edit&action=new" class="btn btn-success"><?php echo __('new_user'); ?></a>
    
    <?php if (empty($users)): ?>
        <p style="margin-top: 20px;"><?php echo __('no_users_found'); ?></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th><?php echo __('username'); ?></th>
                    <th><?php echo __('created_at'); ?></th>
                    <th><?php echo __('last_login'); ?></th>
                    <th><?php echo __('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($user['last_login'] ?? __('never')); ?></td>
                        <td class="action-links">
                            <a href="?page=user_edit&id=<?php echo $user['id']; ?>" class="btn btn-small"><?php echo __('edit'); ?></a>
                            <?php if ($user['username'] !== 'admin'): ?>
                                <a href="?page=user_edit&action=delete&id=<?php echo $user['id']; ?>" 
                                   class="btn btn-small btn-danger" 
                                   onclick="return confirm('<?php echo __('confirm_delete_user'); ?>');"><?php echo __('delete'); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
