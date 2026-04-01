<?php 
$title = 'Edit Profile';
require __DIR__ . '/../Partials/header.php';
require __DIR__ . '/../Partials/navbar.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>My Profile</h2>
                <a href="/" class="btn btn-secondary">Back</a>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <?php if (isset($message)): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="/student/profile">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user->first_name) ?>" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user->last_name) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" 
                                   value="<?= htmlspecialchars($profile->date_of_birth ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bio / About Me</label>
                            <textarea name="bio" class="form-control" rows="3" placeholder="Tell tutors a bit about yourself..."><?= htmlspecialchars($profile->bio ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Save Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../Partials/footer.php'; ?>