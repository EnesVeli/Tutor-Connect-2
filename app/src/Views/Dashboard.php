<?php 
$title = 'Dashboard - Tutor Connect';
require __DIR__ . '/Partials/header.php'; 
require __DIR__ . '/Partials/navbar.php'; 
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm p-4">
                <div class="card-body">
                    <h2 class="card-title text-primary">My Dashboard</h2>
                    <p class="lead text-muted">Select an option below to get started.</p>
                    
                    <div class="row mt-4 g-4">
                        <?php if ($role === 'student'): ?>
                            <div class="col-md-4">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body text-center">
                                        <h4>Find a Tutor</h4>
                                        <p>Search by subject and book a lesson.</p>
                                        <a href="/tutors" class="btn btn-primary w-100">Search Tutors</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body text-center">
                                        <h4>My Bookings</h4>
                                        <p>View your upcoming lessons.</p>
                                        <a href="/bookings" class="btn btn-outline-primary w-100">View Schedule</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body text-center">
                                        <h4>My Profile</h4>
                                        <p>Update your personal details.</p>
                                        <a href="/student/profile" class="btn btn-outline-secondary w-100">Edit Profile</a>
                                    </div>
                                </div>
                            </div>

                        <?php elseif ($role === 'tutor'): ?>
                            <div class="col-md-4">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body text-center">
                                        <h4>My Profile</h4>
                                        <p>Update your bio, subjects, and hourly rate.</p>
                                        <a href="/profile" class="btn btn-primary w-100">Edit Profile</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body text-center">
                                        <h4>Upcoming Lessons</h4>
                                        <p>See which students have booked you.</p>
                                        <a href="/bookings" class="btn btn-outline-primary w-100">View Schedule</a>
                                    </div>
                                </div>
                            </div>

                        <?php elseif ($role === 'admin'): ?>
                            <div class="col-md-4">
                                <div class="card h-100 border-danger border-2">
                                    <div class="card-body text-center">
                                        <h4 class="text-danger">Manage Users</h4>
                                        <p>View, edit, or delete users.</p>
                                        <a href="/admin/users" class="btn btn-danger w-100">User Management</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card h-100 border-info border-2">
                                    <div class="card-body text-center">
                                        <h4 class="text-info">Platform Stats</h4>
                                        <p>View bookings and earnings reports.</p>
                                        <a href="/admin/statistics" class="btn btn-info text-white w-100">View Statistics</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/Partials/footer.php'; ?>