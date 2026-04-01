<?php 
$title = 'Find a Tutor';
require __DIR__ . '/../Partials/header.php';
require __DIR__ . '/../Partials/navbar.php';
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Find a Tutor</h2>
        <a href="/" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card p-3 bg-white shadow-sm">
                <form method="GET" action="/tutors" class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Subject</label>
                        <select name="subject" class="form-select">
                            <option value="">All Subjects</option>
                            <?php 
                            $subjects = ['Math', 'English', 'Physics', 'Chemistry', 'Biology', 'Computer Science', 'Music', 'Art'];
                            foreach ($subjects as $sub) {
                                $isSelected = (isset($selectedSubject) && $selectedSubject === $sub) ? 'selected' : '';
                                echo "<option value='$sub' $isSelected>$sub</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Min Price (€)</label>
                        <select name="min_price" class="form-select">
                            <option value="">€ 0</option>
                            <?php 
                            $prices = [5, 10, 15, 20, 25, 30, 35, 40, 45, 50];
                            foreach($prices as $p) {
                                $isSelected = (isset($selectedMinPrice) && (float)$selectedMinPrice == $p) ? 'selected' : '';
                                echo "<option value='$p' $isSelected>€ $p</option>"; 
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Max Price (€)</label>
                        <select name="max_price" class="form-select">
                            <option value="">Any</option>
                            <?php 
                            $prices = [5, 10, 15, 20, 25, 30, 35, 40, 45, 50];
                            foreach($prices as $p) {
                                $isSelected = (isset($selectedMaxPrice) && (float)$selectedMaxPrice == $p) ? 'selected' : '';
                                echo "<option value='$p' $isSelected>€ $p</option>"; 
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row" id="tutor-results">
        <?php foreach ($tutors as $tutor): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm position-relative">

                <img src="https://ui-avatars.com/api/?name=<?= urlencode($tutor['first_name'] . '+' . $tutor['last_name']) ?>&background=random&size=64"
                    alt="Profile picture of <?= htmlspecialchars($tutor['first_name'] . ' ' . $tutor['last_name']) ?>"
                    class="rounded-circle position-absolute top-0 end-0 m-3 border border-white shadow-sm" width="50"
                    height="50">

                <div class="card-header bg-white pt-4">
                    <h5 class="mb-0"><?= htmlspecialchars($tutor['first_name'] . ' ' . $tutor['last_name']) ?></h5>
                    <small class="text-muted"><?= htmlspecialchars($tutor['subject']) ?></small>
                </div>
                <div class="card-body">
                    <p class="card-text text-truncate"><?= htmlspecialchars($tutor['bio']) ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-success">€<?= htmlspecialchars($tutor['hourly_rate']) ?>/hr</span>
                        <span class="badge bg-info text-dark"><?= htmlspecialchars($tutor['experience_years']) ?> Years
                            Exp.</span>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <a href="/book?tutor_id=<?= $tutor['profile_id'] ?? $tutor['user_id'] ?>"
                        class="btn btn-primary w-100">Book Lesson</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($tutors)): ?>
        <div class="col-12">
            <div class="alert alert-warning text-center">No tutors found matching your criteria.</div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="/assets/js/search.js"></script>

<?php require __DIR__ . '/../Partials/footer.php'; ?>