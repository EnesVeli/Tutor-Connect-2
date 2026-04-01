<?php 
$title = 'Edit User';
require __DIR__ . '/../Partials/header.php';
require __DIR__ . '/../Partials/navbar.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4>Edit User #<?= $user->id ?></h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control"
                                value="<?= htmlspecialchars($user->first_name) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control"
                                value="<?= htmlspecialchars($user->last_name) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($user->email) ?>" required>
                        </div>

                        <?php if ($user->role !== 'admin'): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">Content Control (Bio)</label>
                            <textarea name="bio" class="form-control"
                                rows="4"><?= htmlspecialchars($user->bio ?? '') ?></textarea>
                            <div class="form-text">Admins can edit or clear inappropriate content here.</div>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between">
                            <a href="/admin/users" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../Partials/footer.php'; ?>