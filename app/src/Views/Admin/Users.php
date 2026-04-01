<?php 
$title = 'Manage Users';
require __DIR__ . '/../Partials/header.php';
require __DIR__ . '/../Partials/navbar.php';
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>User Management</h2>
        <a href="/" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Subject</th>
                            <th>Bio</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user->id ?></td>
                            <td><?= htmlspecialchars($user->first_name . ' ' . $user->last_name) ?></td>
                            <td><?= htmlspecialchars($user->email) ?></td>
                            <td>
                                <span
                                    class="badge bg-<?= $user->role === 'admin' ? 'danger' : ($user->role === 'tutor' ? 'primary' : 'success') ?>">
                                    <?= ucfirst($user->role) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($user->subject)): ?>
                                <span class="badge bg-info text-dark"><?= htmlspecialchars($user->subject) ?></span>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small
                                    class="text-muted"><?= htmlspecialchars(substr($user->bio ?? '', 0, 30)) ?>...</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="/admin/users/edit?id=<?= $user->id ?>"
                                        class="btn btn-sm btn-outline-primary">Edit</a>

                                    <?php if (!empty($user->profile_id)): ?>
                                    <form method="POST" action="/admin/profiles/delete"
                                        onsubmit="return confirm('Delete this specific subject?');"
                                        style="display:inline;">
                                        <input type="hidden" name="profile_id" value="<?= $user->profile_id ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning"
                                            title="Delete Subject Only">Delete Subject</button>
                                    </form>
                                    <?php endif; ?>

                                    <form method="POST" action="/admin/users/delete"
                                        onsubmit="return confirm('DANGER: This deletes the WHOLE ACCOUNT and ALL subjects!');"
                                        style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $user->id ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            title="Delete Entire Account">Delete User</button>
                                    </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../Partials/footer.php'; ?>