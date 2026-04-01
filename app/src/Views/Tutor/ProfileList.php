<?php 
$title = 'My Profiles';
require __DIR__ . '/../Partials/header.php';
require __DIR__ . '/../Partials/navbar.php';
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Teaching Profiles</h2>
        <div>
            <a href="/" class="btn btn-secondary me-2">Back to Dashboard</a> <a href="/profile/edit"
                class="btn btn-success">+ Add New Subject</a>
        </div>
    </div>

    <div class="row">
        <?php foreach ($profiles as $p): ?>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= htmlspecialchars($p->subject) ?></h5>
                    <span class="badge bg-primary">€<?= $p->hourly_rate ?>/hr</span>
                </div>
                <div class="card-body">
                    <p><?= htmlspecialchars($p->bio) ?></p>
                    <small class="text-muted">
                        <i class="bi bi-clock"></i> <?= $p->availability_start ?> - <?= $p->availability_end ?>
                        <br>
                        <strong>Days:</strong> <?= htmlspecialchars($p->available_days ?? 'All') ?>
                    </small>
                </div>
                <div class="card-footer bg-white">
                    <a href="/profile/edit?id=<?= $p->id ?>" class="btn btn-sm btn-outline-primary w-100">Edit This
                        Profile</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($profiles)): ?>
        <div class="col-12">
            <div class="alert alert-info">You haven't set up any teaching profiles yet.</div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../Partials/footer.php'; ?>