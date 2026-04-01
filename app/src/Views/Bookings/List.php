<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Schedule</h2>
            <a href="/" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Date & Time</th>
                            <th>With</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= date('M d, Y', strtotime($booking['scheduled_at'])) ?></div>
                                <div class="small text-muted"><?= date('H:i', strtotime($booking['scheduled_at'])) ?>
                                </div>
                            </td>

                            <td>
                                <div class="fw-bold">
                                    <?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></div>

                                <?php if ($_SESSION['user_role'] === 'tutor' && !empty($booking['date_of_birth'])): ?>
                                <?php 
                                    try {
                                        $dob = new DateTime($booking['date_of_birth']);
                                        $now = new DateTime();
                                        $age = $now->diff($dob)->y;
                                        echo "<div class='small text-muted'>$age Years Old</div>";
                                    } catch (Exception $e) {
                                    }
                                ?>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($booking['email']) ?></td>

                            <td><?= htmlspecialchars($booking['student_comment']) ?></td>

                            <td class="text-end">
                                <?php if ($_SESSION['user_role'] === 'tutor' && $booking['status'] === 'pending'): ?>
                                <form method="POST" action="/booking/update" class="d-inline-flex gap-2">
                                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                    <button name="status" value="confirmed"
                                        class="btn btn-sm btn-success">Accept</button>
                                    <button name="status" value="cancelled"
                                        class="btn btn-sm btn-danger">Reject</button>
                                </form>
                                <?php else: ?>
                                <?php 
                                    $statusColor = match($booking['status']) {
                                        'confirmed' => 'success',
                                        'cancelled' => 'danger',
                                        'completed' => 'secondary',
                                        default => 'warning'
                                    };
                                ?>
                                <span
                                    class="badge bg-<?= $statusColor ?> text-dark bg-opacity-25 border border-<?= $statusColor ?>">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (empty($bookings)): ?>
                <div class="text-center p-4">
                    <p class="text-muted mb-0">No bookings found.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>