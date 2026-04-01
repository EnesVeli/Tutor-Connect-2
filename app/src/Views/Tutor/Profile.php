<?php 
$myDays = isset($profile->available_days) ? explode(',', $profile->available_days) : [];

$title = isset($profile->id) ? 'Edit Profile' : 'Create Profile';
require __DIR__ . '/../Partials/header.php';
require __DIR__ . '/../Partials/navbar.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4><?= $title ?></h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Subject</label>
                                <select name="subject" class="form-select" required>
                                    <option value="">-- Select Subject --</option>
                                    <?php 
                                    $subjects = ['Math', 'English', 'Physics', 'Chemistry', 'Biology', 'Computer Science', 'Music', 'Art'];
                                    foreach($subjects as $s) {
                                        $sel = ($profile->subject ?? '') == $s ? 'selected' : '';
                                        echo "<option value='$s' $sel>$s</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col">
                                <label class="form-label">Hourly Rate (€)</label>
                                <select name="hourly_rate" class="form-select" required>
                                    <option value="">-- Select Rate --</option>
                                    <?php 
                                    $prices = [5, 10, 15, 20, 25, 30, 35, 40, 45, 50];
                                    foreach ($prices as $p) {
                                        $val = (int)($profile->hourly_rate ?? 0);
                                        $selected = $val === $p ? 'selected' : '';
                                        echo "<option value='$p' $selected>€ $p</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Available Days</label>
                            <div class="d-flex flex-wrap gap-3">
                                <?php 
                                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                foreach ($days as $day): 
                                    $checked = in_array($day, $myDays) ? 'checked' : '';
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" value="<?= $day ?>"
                                        id="day_<?= $day ?>" <?= $checked ?>>
                                    <label class="form-check-label" for="day_<?= $day ?>"><?= $day ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="form-label fw-bold">Availability (Working Hours)</label>
                            <div class="col">
                                <label class="small text-muted">From</label>
                                <select name="availability_start" class="form-select">
                                    <?php for($i=6; $i<=18; $i++): $time = sprintf('%02d:00', $i); ?>
                                    <option value="<?= $time ?>"
                                        <?= ($profile->availability_start ?? '09:00') == $time ? 'selected' : '' ?>>
                                        <?= $time ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col">
                                <label class="small text-muted">To</label>
                                <select name="availability_end" class="form-select">
                                    <?php for($i=10; $i<=22; $i++): $time = sprintf('%02d:00', $i); ?>
                                    <option value="<?= $time ?>"
                                        <?= ($profile->availability_end ?? '17:00') == $time ? 'selected' : '' ?>>
                                        <?= $time ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Experience (Years)</label>
                            <input type="number" name="experience_years" class="form-control" min="0"
                                value="<?= $profile->experience_years ?? '' ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bio for this Subject</label>
                            <textarea name="bio" class="form-control"
                                rows="3"><?= htmlspecialchars($profile->bio ?? '') ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/profile" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../Partials/footer.php'; ?>