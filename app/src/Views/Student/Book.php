<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book a Session</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4>Book a Lesson</h4>
                    </div>
                    <div class="card-body">
                        <p>Booking with Tutor <strong><?= htmlspecialchars($tutorName) ?></strong></p>
                        
                        <div class="alert alert-info">
                            <small>Available Days:</small><br>
                            <strong><?= htmlspecialchars($tutorProfile->available_days ?? 'Mon,Tue,Wed,Thu,Fri') ?></strong>
                        </div>

                        <form method="POST" action="/book/payment">
                            <input type="hidden" name="tutor_id" value="<?= htmlspecialchars($_GET['tutor_id']) ?>">

                            <div class="mb-3">
                                <label class="form-label">Select Date</label>
                                <input type="date" name="date" id="dateInput" class="form-control" min="<?= date('Y-m-d') ?>" required>
                                <div id="dateError" class="text-danger mt-1" style="display:none;">
                                    Tutor is not available on this day.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select Time Slot</label>
                                <select name="time" class="form-select" required>
                                    <option value="">-- Choose a time --</option>
                                    <?php 
                                    $startHour = (int)substr($tutorProfile->availability_start ?? '09:00', 0, 2);
                                    $endHour = (int)substr($tutorProfile->availability_end ?? '17:00', 0, 2);

                                    for ($h = $startHour; $h < $endHour; $h++) {
                                        foreach (['00', '30'] as $m) {
                                            $time = sprintf('%02d:%s', $h, $m);
                                            echo "<option value='$time'>$time</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea name="student_comment" class="form-control" rows="3" placeholder="I need help with..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Confirm Booking</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Convert "Mon,Tue" string to JS Array of numbers (Sun=0, Mon=1, etc.)
    $dayMap = ['Sun'=>0, 'Mon'=>1, 'Tue'=>2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6];
    $availableStr = $tutorProfile->available_days ?? 'Mon,Tue,Wed,Thu,Fri';
    $daysArray = explode(',', $availableStr);
    $allowedIndices = [];
    
    foreach($daysArray as $d) {
        $d = trim($d);
        if(isset($dayMap[$d])) {
            $allowedIndices[] = $dayMap[$d];
        }
    }
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('dateInput');
            const errorMsg = document.getElementById('dateError');
            const allowedDays = <?= json_encode($allowedIndices) ?>; 

            if (allowedDays.length === 0) return;

            dateInput.addEventListener('change', function() {
                if (!this.value) return;

                const parts = this.value.split('-');
                const selectedDate = new Date(parts[0], parts[1] - 1, parts[2]); 
                const dayIndex = selectedDate.getDay(); // 0-6

                if (!allowedDays.includes(dayIndex)) {
                    errorMsg.style.display = 'block';
                    alert('This tutor is only available on: <?= $availableStr ?>');
                    this.value = ''; 
                } else {
                    errorMsg.style.display = 'none';
                }
            });
        });
    </script>

</body>
</html>