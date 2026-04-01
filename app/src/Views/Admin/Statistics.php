<?php 
$title = 'Platform Statistics';
require __DIR__ . '/../Partials/header.php';
require __DIR__ . '/../Partials/navbar.php';
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Platform Statistics</h2>
        <a href="/admin/users" class="btn btn-secondary">Back to Users</a>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card bg-primary text-white p-3 text-center">
                <h3><?= $stats['total_users'] ?></h3>
                <div>Total Users</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white p-3 text-center">
                <h3>€<?= number_format($stats['total_earnings'], 2) ?></h3>
                <div>Platform Earnings</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark p-3 text-center">
                <h3><?= $stats['total_bookings'] ?></h3>
                <div>Total Bookings</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white p-3 text-center">
                <h3><?= $stats['total_tutors'] ?></h3>
                <div>Active Tutors</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Most Popular Tutors</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tutor Name</th>
                            <th>Bookings Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['tutors_list'] as $tutor): ?>
                        <tr>
                            <td><?= htmlspecialchars($tutor->first_name . ' ' . $tutor->last_name) ?></td>
                            <td>
                                <span class="badge bg-primary rounded-pill">
                                    <?= $tutor->booking_count ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($stats['tutors_list'])): ?>
                        <tr>
                            <td colspan="2" class="text-center">No bookings yet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
<?php require __DIR__ . '/../Partials/footer.php'; ?>